<?php

namespace App\Service;

use App\Entity\GameUser;
use App\Entity\SteamAccount;
use App\Entity\Game;
use App\Entity\LibraryGame;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SteamAuthService
{
    private const STEAM_OPENID_URL = 'https://steamcommunity.com/openid/login';
    private const STEAM_API_URL = 'https://api.steampowered.com';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private HttpClientInterface $httpClient,
        private string $steamApiKey,
        private ?LoggerInterface $logger = null
    ) {
    }

    /**
     * Génère l'URL de redirection vers Steam
     */
    public function generateLoginUrl(string $returnUrl): string
    {
        // openid.realm = racine du site (ex: http://localhost:8000/)
        $parsedUrl = parse_url($returnUrl);
        $realm = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
        if (isset($parsedUrl['port'])) {
            $realm .= ':' . $parsedUrl['port'];
        }
        $realm .= '/';

        $params = [
            'openid.ns'         => 'http://specs.openid.net/auth/2.0',
            'openid.mode'       => 'checkid_setup',
            'openid.return_to'  => $returnUrl,
            'openid.realm'      => $realm,
            'openid.identity'   => 'http://specs.openid.net/auth/2.0/identifier_select',
            'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
        ];

        return self::STEAM_OPENID_URL . '?' . http_build_query($params);
    }

    /**
     * Valide la réponse OpenID de Steam
     */
    public function validateOpenIdResponse(Request $request): ?string
    {
        // IMPORTANT : ne pas utiliser $request->query->all() car Symfony/PHP
        // convertit les points (.) des noms de paramètres en underscores (_),
        // ce qui casse les clés "openid.*" attendues par Steam.
        // On parse donc la query string brute pour préserver les noms exacts.
        $params = [];
        foreach (explode('&', $request->getQueryString() ?? '') as $pair) {
            if ($pair === '') {
                continue;
            }
            $parts = explode('=', $pair, 2);
            $key = urldecode($parts[0]);
            $value = isset($parts[1]) ? urldecode($parts[1]) : '';
            $params[$key] = $value;
        }

        $params['openid.mode'] = 'check_authentication';

        try {
            $response = $this->httpClient->request('POST', self::STEAM_OPENID_URL, [
                'body' => $params,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);

            $content = $response->getContent(false);
        } catch (\Throwable $e) {
            $this->logger?->error('Steam OpenID: erreur HTTP', ['error' => $e->getMessage()]);
            return null;
        }

        $this->logger?->info('Steam OpenID: réponse validation', [
            'content' => $content,
            'claimed_id' => $params['openid.claimed_id'] ?? null,
        ]);

        if (str_contains($content, 'is_valid:true')) {
            // Extraire le SteamID de l'URL claimed_id
            $claimedId = $params['openid.claimed_id'] ?? '';
            preg_match('/\/(\d+)$/', $claimedId, $matches);

            return $matches[1] ?? null;
        }

        return null;
    }

    /**
     * Récupère les informations du profil Steam
     */
    public function getSteamProfile(string $steamId): ?array
    {
        try {
            $response = $this->httpClient->request('GET', self::STEAM_API_URL . '/ISteamUser/GetPlayerSummaries/v0002/', [
                'query' => [
                    'key' => $this->steamApiKey,
                    'steamids' => $steamId,
                ],
            ]);

            $data = $response->toArray();
            return $data['response']['players'][0] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Récupère la bibliothèque de jeux Steam
     */
    public function getSteamGames(string $steamId): ?array
    {
        try {
            $response = $this->httpClient->request('GET', self::STEAM_API_URL . '/IPlayerService/GetOwnedGames/v0001/', [
                'query' => [
                    'key' => $this->steamApiKey,
                    'steamid' => $steamId,
                    'include_appinfo' => 1,
                    'include_played_free_games' => 1,
                ],
            ]);

            $data = $response->toArray();
            return $data['response']['games'] ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Lie un compte Steam à un utilisateur
     */
    public function linkSteamAccount(GameUser $user, string $steamId, ?array $profileData = null): SteamAccount
    {
        $steamAccount = $user->getSteamAccount();
        
        if (!$steamAccount) {
            $steamAccount = new SteamAccount();
            $steamAccount->setUser($user);
        }

        $steamAccount->setSteamId($steamId);
        $steamAccount->setLinkedAt(new \DateTime());

        if ($profileData) {
            $steamAccount->setPersonaName($profileData['personaname'] ?? null);
            $steamAccount->setAvatar($profileData['avatarfull'] ?? null);
            $steamAccount->setProfileUrl($profileData['profileurl'] ?? null);
        }

        $this->entityManager->persist($steamAccount);
        $this->entityManager->flush();

        return $steamAccount;
    }

    /**
     * Synchronise les jeux Steam avec la bibliothèque utilisateur
     */
    public function syncSteamGames(GameUser $user, string $steamId): int
    {
        $steamGames = $this->getSteamGames($steamId);
        $this->logger?->info('Steam sync: jeux retournés par l\'API', [
            'steamId' => $steamId,
            'count' => is_array($steamGames) ? count($steamGames) : 0,
            'names' => array_map(fn($g) => $g['name'] ?? '???', $steamGames ?? []),
        ]);
        $importedCount = 0;

        foreach ($steamGames as $steamGame) {
            $gameName = $steamGame['name'] ?? '';
            $appId = $steamGame['appid'] ?? 0;

            if (empty($gameName) || $appId === 0) {
                $this->logger?->info('Steam sync: jeu ignoré (nom/appid manquant)', [
                    'appid' => $appId,
                    'name' => $gameName,
                ]);
                continue;
            }

            // Vérifier si le jeu existe déjà dans la bibliothèque de l'utilisateur
            $existingLibraryGame = $this->entityManager->getRepository(LibraryGame::class)
                ->createQueryBuilder('lg')
                ->join('lg.game', 'g')
                ->where('lg.user = :user')
                ->andWhere('g.title = :title')
                ->setParameter('user', $user)
                ->setParameter('title', $gameName)
                ->getQuery()
                ->setMaxResults(1)
                ->getOneOrNullResult();

            if ($existingLibraryGame) {
                // Le jeu existe déjà dans la bibliothèque
                continue;
            }

            $this->logger?->info('Steam sync: nouveau jeu à importer', ['name' => $gameName, 'appid' => $appId]);

            // Vérifier si le jeu existe déjà dans la base de données
            $existingGame = $this->entityManager->getRepository(Game::class)
                ->findOneBy(['title' => $gameName]);

            if (!$existingGame) {
                // Créer un nouveau jeu
                $existingGame = new Game();
                $existingGame->setTitle($gameName);
                $existingGame->setDeveloper($steamGame['developer'] ?? null);
                $existingGame->setPublisher($steamGame['publisher'] ?? null);
                $existingGame->setMode('steam');
                
                // Convertir le temps de jeu Steam en minutes
                $playtimeMinutes = $steamGame['playtime_forever'] ?? 0;
                $existingGame->setEstimatedPlaytime($playtimeMinutes);

                $this->entityManager->persist($existingGame);
            }

            // Créer l'entrée dans la bibliothèque utilisateur
            $libraryGame = new LibraryGame();
            $libraryGame->setUser($user);
            $libraryGame->setGame($existingGame);
            $libraryGame->setStatus('possédé');
            
            // Utiliser le temps de jeu Steam comme temps personnel
            $playtimeMinutes = $steamGame['playtime_forever'] ?? 0;
            $libraryGame->setPlaytime($playtimeMinutes);

            $this->entityManager->persist($libraryGame);
            $importedCount++;
        }

        // Mettre à jour la date de dernière synchronisation
        $steamAccount = $user->getSteamAccount();
        if ($steamAccount) {
            $steamAccount->setLastSyncAt(new \DateTime());
            $this->entityManager->persist($steamAccount);
        }

        $this->entityManager->flush();

        $this->logger?->info('Steam sync: terminé', ['imported' => $importedCount]);

        return $importedCount;
    }
}