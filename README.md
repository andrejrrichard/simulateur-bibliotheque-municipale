# Simulateur de bibliothèque municipale

Application web de gestion d'une bibliothèque permettant de gérer les usagers, les documents, les prêts, les réservations et l'historique des transactions.

L'application propose des interfaces et des fonctionnalités différentes selon le rôle de l'utilisateur : membre, employé ou administrateur.

## Fonctionnalités

### Membres

* Authentification
* Consultation des documents de la bibliothèque
* Recherche de documents
* Filtrage des documents par état :

  * disponible
  * réservé
  * prêté
  * en retard
* Consultation de ses documents
* Réservation d'un document
* Annulation d'une réservation
* Consultation de l'état d'un document

### Employés

En plus des fonctionnalités accessibles aux membres :

* Consultation des usagers
* Consultation des membres
* Consultation des documents associés à un membre
* Prêt d'un document à un membre
* Annulation d'un prêt
* Enregistrement du retour d'un document
* Réservation d'un document pour un membre

### Administrateurs

En plus des fonctionnalités accessibles aux employés :

* Création d'usagers
* Consultation des différents types d'usagers
* Consultation de l'ensemble des transactions
* Filtrage des transactions par type d'action
* Consultation de l'historique des opérations

## Technologies

* **PHP**
* **MySQL**
* **PDO**
* **Bootstrap 5**
* **HTML / CSS**
* **JavaScript**

## Architecture

Le projet sépare les principales responsabilités de l'application :

```text
.
├── controllers/
│   ├── document.php
│   ├── login.php
│   ├── logout.php
│   ├── transaction.php
│   └── usager.php
│
├── includes/
│   ├── auth.php
│   ├── bd.php
│   ├── config.php
│   └── initApp.php
│
├── layouts/
│   ├── footer.php
│   ├── header.php
│   ├── message.php
│   └── siteFrame.php
│
├── models/
│   ├── Document.php
│   ├── Transaction.php
│   └── Usager.php
│
├── pages/
│   ├── 403.php
│   ├── 404.php
│   ├── accueil.php
│   ├── document.php
│   ├── login.php
│   ├── logout.php
│   ├── transaction.php
│   └── usager.php
│
├── public/
│   ├── css/
│   ├── images/
│   └── js/
│
├── .env.example
├── .gitignore
├── bibliothequeMunicipale.sql
├── composer.json
└── index.php
```

### Contrôleurs

Les contrôleurs traitent les requêtes de l'utilisateur et coordonnent les opérations entre l'interface et les modèles.

### Modèles

Les modèles représentent les principales entités de l'application :

* usagers
* documents
* transactions

Ils encapsulent notamment les opérations effectuées sur les données.

### Middlewares et services communs

Le dossier `includes` contient notamment :

* la gestion de l'authentification;
* la connexion à la base de données;
* la configuration de l'application;
* l'initialisation des données nécessaires au fonctionnement.

### Pages et layouts

Les pages constituent les différentes interfaces de l'application tandis que les layouts regroupent les éléments communs de l'interface.

## Base de données

La base de données MySQL modélise notamment :

* les usagers et leurs rôles;
* les documents;
* les catégories de documents;
* les types de documents;
* les genres;
* les prêts;
* les réservations;
* les retours;
* l'historique des transactions.

Le script `bibliothequeMunicipale.sql` initialise le schéma et fournit des données de démonstration.

La base utilise notamment :

* clés primaires et étrangères;
* contraintes d'unicité;
* vues SQL;
* procédures stockées;
* contraintes d'intégrité référentielle.

## Authentification

Les mots de passe des utilisateurs sont stockés sous forme de hash à l'aide de `password_hash()` et vérifiés avec les mécanismes natifs de PHP.

Les informations de connexion à la base de données et les autres paramètres sensibles sont fournis par des variables d'environnement et ne sont pas stockés dans le dépôt.

## Installation

### Prérequis

* PHP 8.5 ou version compatible
* MySQL 8.0 ou version compatible
* Composer

### 1. Cloner le dépôt

```bash
git clone <URL_DU_DEPOT>
cd <NOM_DU_DEPOT>
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Créer la base de données

Importer le fichier :

```text
bibliothequeMunicipale.sql
```

dans MySQL.

### 4. Configurer les variables d'environnement

Copier `.env.example` vers `.env` :

```bash
cp .env.example .env
```

Puis modifier les valeurs correspondant à votre environnement local.

Le fichier `.env` ne doit jamais être ajouté au dépôt Git.

### 5. Démarrer l'application

Depuis la racine du projet :

```bash
php -S localhost:3000
```

L'application sera accessible à :

```text
http://localhost:3000
```

## Données de démonstration

Au premier démarrage, l'application initialise automatiquement les données nécessaires à son fonctionnement et crée un compte administrateur à partir de la variable d'environnement `ADMIN_PASSWORD`.

## Sécurité

Le projet utilise notamment :

* `password_hash()` pour le stockage des mots de passe;
* PDO avec requêtes préparées;
* variables d'environnement pour les informations sensibles;
* contrôle d'accès selon le rôle de l'utilisateur;
* gestion des sessions;
* contraintes d'intégrité référentielle dans MySQL.

> Cette application est un projet de démonstration. Elle ne doit pas être utilisée telle quelle pour gérer de véritables données personnelles ou les opérations d'une bibliothèque en production.