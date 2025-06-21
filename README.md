# Biblios - Application de Gestion de Bibliothèque

## Description du Projet
Biblios est une application web développée avec Symfony qui permet la gestion d'une bibliothèque numérique. Elle offre des fonctionnalités pour gérer les livres, les auteurs, les éditeurs et les commentaires des utilisateurs.

## Structure du Projet

### Technologies Utilisées
- Symfony (Framework PHP)
- Doctrine ORM
- Twig (Moteur de templates)
- Asset Mapper
- Système d'authentification Symfony Security

### Architecture du Projet

#### Entités Principales
- **User** : Gestion des utilisateurs et authentification
- **Book** : Gestion des livres
- **Author** : Gestion des auteurs
- **Editor** : Gestion des éditeurs
- **Comment** : Système de commentaires

#### Contrôleurs
- **MainController** : Page d'accueil et fonctionnalités principales
- **BookController** : Gestion des livres
- **SecurityController** : Authentification
- **RegistrationController** : Inscription des utilisateurs
- **ProfileController** : Gestion des profils utilisateurs

#### Formulaires
- AuthorType
- BookType
- CommentType
- EditorType
- RegistrationFormType

#### Énumérations
- BookStatus : Gestion des états des livres
- CommentStatus : Gestion des états des commentaires

### Organisation des Dossiers
```
├── assets/           # Fichiers frontend (JS, CSS)
├── config/           # Configuration Symfony
├── migrations/       # Migrations de base de données
├── public/           # Point d'entrée public
├── src/              # Code source PHP
│   ├── Controller/   # Contrôleurs
│   ├── Entity/       # Entités Doctrine
│   ├── Enum/         # Énumérations
│   ├── Form/         # Types de formulaires
│   ├── Repository/   # Repositories Doctrine
│   └── Security/     # Sécurité et authentification
├── templates/        # Templates Twig
└── translations/     # Fichiers de traduction
```

## Fonctionnalités Principales

### Gestion des Livres
- Ajout, modification et suppression de livres
- Association avec des auteurs et éditeurs
- Système de statut pour les livres

### Gestion des Utilisateurs
- Inscription et authentification
- Gestion des profils
- Système de rôles et permissions

### Système de Commentaires
- Ajout de commentaires sur les livres
- Modération des commentaires
- États des commentaires

## Configuration et Installation

### Prérequis
- PHP 8.x
- Composer
- Symfony CLI
- Base de données (MySQL/PostgreSQL)

### Installation
1. Cloner le dépôt
2. Installer les dépendances : `composer install`
3. Configurer le fichier `.env`
4. Créer la base de données : `php bin/console doctrine:database:create`
5. Exécuter les migrations : `php bin/console doctrine:migrations:migrate`

### Lancement du Serveur de Développement
```bash
symfony server:start
```

## Sécurité
- Authentification utilisateur
- Protection CSRF
- Système de voteurs (Voters)
- Validation des formulaires

## Interface Utilisateur
- Design responsive
- Templates Twig organisés
- Assets gérés via Asset Mapper
- Styles CSS personnalisés

## Base de Données
- Utilisation de Doctrine ORM
- Migrations pour le versioning de la base de données
- Repositories personnalisés pour les requêtes complexes

## Maintenance et Évolution
- Structure modulaire facilitant les extensions
- Code organisé suivant les bonnes pratiques Symfony
- Documentation intégrée dans le code
- Tests unitaires et fonctionnels (à implémenter)