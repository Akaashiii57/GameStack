# GameStack - Gestionnaire de jeux vidéo

GameStack est une application web moderne permettant de centraliser et gérer votre collection de jeux vidéo, avec une synchronisation automatique Steam et une interface utilisateur élégante.

## 🎮 Fonctionnalités principales

### 🔗 Synchronisation Steam
- Import automatique de votre bibliothèque Steam complète
- Récupération des données détaillées (jaquettes, développeurs, éditeurs, descriptions)
- Détection automatique des modes de jeu (Solo, Multijoueur, Coopératif)
- Resynchronisation à la demande

### 📚 Gestion de bibliothèque
- Interface unifiée pour jeux Steam et manuels
- Système de tri A-Z / Z-A
- Limitation des descriptions à 2 lignes avec "..."
- Affichage des informations complètes (développeur, éditeur, date de sortie)

### 📊 Suivi personnalisé
- Gestion des statuts : À faire, En cours, Terminé, Abandonné
- Système d'évaluation personnel (1-10)
- Enregistrement du temps de jeu
- Dates de début/fin de jeu

### 🎨 Direction artistique
- Design éditorial arcade avec thème sombre chaleureux
- Couleurs : Orange #E8783C sur fond #100E0B
- Polices : Fraunces Regular pour les titres, Manrope pour le texte
- Interface responsive et moderne

## 🚀 Installation

### Prérequis
- PHP 8.2+
- Composer
- SQLite (ou autre base de données)
- Node.js (pour les assets)

### Installation
```bash
# Cloner le projet
git clone <repository-url>
cd GameStack

# Installer les dépendances PHP
composer install

# Installer les dépendances Node.js
npm install

# Configurer la base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Charger les fixtures (optionnel)
php bin/console doctrine:fixtures:load

# Construire les assets
npm run build

# Lancer le serveur
symfony server:start
```

## 📁 Structure du projet

```
GameStack/
├── src/
│   ├── Controller/          # Contrôleurs Symfony
│   ├── Entity/             # Entités Doctrine
│   ├── Repository/          # Repositories
│   ├── Service/            # Services métier
│   └── Security/           # Authentification
├── templates/              # Templates Twig
├── assets/                 # Assets frontend
├── public/                 # Fichiers publics
├── migrations/             # Migrations Doctrine
└── var/                   # Cache et données
```

## 🔧 Configuration

### Variables d'environnement
Créer un fichier `.env.local` :
```env
# Clé API Steam (optionnelle pour développement)
STEAM_API_KEY=votre_cle_api_steam

# Configuration base de données
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
```

### Commandes disponibles
```bash
# Synchroniser les jeux Steam d'un utilisateur
php bin/console app:sync-steam-games <steamId>

# Mettre à jour les détails des jeux Steam
php bin/console app:update-steam-details

# Migrer les statuts des jeux
php bin/console app:migrate-steam-status
```

## 🎯 Utilisation

### Première connexion
1. Créer un compte sur GameStack
2. Lier votre compte Steam via le bouton dédié
3. Votre bibliothèque Steam est automatiquement importée

### Gestion des jeux
- **Ajout manuel** : Via le formulaire de recherche
- **Statuts** : Modifiable depuis la page de détail d'un jeu
- **Notes** : Système d'évaluation de 1 à 10
- **Temps de jeu** : Enregistrement manuel ou automatique

### Interface
- **Page d'accueil** : Présentation complète avec sections détaillées
- **Bibliothèque** : Vue grille avec filtres et recherche
- **Détail jeu** : Informations complètes et gestion

## 🔐 Sécurité

- Authentification Symfony sécurisée
- Validation OpenID Steam robuste
- Protection CSRF sur tous les formulaires
- Gestion sécurisée des sessions

## 🎨 Design System

### Couleurs
- `--bg`: #100E0B (fond principal)
- `--accent`: #E8783C (orange principal)
- `--text`: #F2EBDC (texte principal)
- `--surface`: #1A1714 (surfaces)

### Polices
- **Fraunces** : Titres et éléments d'affichage
- **Manrope** : Texte normal et contenu
- **JetBrains Mono** : Éléments techniques

## 🐛 Développement

### Tests
```bash
# Lancer les tests PHPUnit
php bin/phpunit

# Vérifier le code style
php bin/console lint:twig templates/
php bin/console lint:yaml config/
```

### Débogage
- Logs disponibles dans `var/log/`
- Profiler Symfony activé en développement
- Commandes de debug disponibles


## 🤝 Contribution

Les contributions sont les bienvenues ! Voir le fichier `CONTRIBUTING.md` pour les guidelines.


---

**GameStack** - Organisez votre passion du gaming 🎮
