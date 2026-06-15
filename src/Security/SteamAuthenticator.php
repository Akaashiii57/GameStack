<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * Authenticator dédié à la connexion programmatique via Steam.
 * Il est uniquement invoqué via UserAuthenticatorInterface::authenticateUser(),
 * jamais déclenché automatiquement sur une requête.
 */
class SteamAuthenticator extends AbstractAuthenticator
{
    public function __construct(private RouterInterface $router)
    {
    }

    /**
     * Cet authenticator n'intercepte jamais de requête automatiquement.
     */
    public function supports(Request $request): ?bool
    {
        return false;
    }

    public function authenticate(Request $request): Passport
    {
        // Jamais appelé automatiquement (supports() retourne false)
        throw new AuthenticationException('SteamAuthenticator ne gère pas les requêtes directes.');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->router->generate('app_home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new RedirectResponse($this->router->generate('app_login'));
    }
}
