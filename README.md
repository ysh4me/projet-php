# Buddy Shotz - Application de Gestion de Photos

## Introduction

Buddy Shotz est une application web moderne de gestion de photos développée avec PHP 8. Elle permet aux utilisateurs de gérer leurs collections de photos, créer des albums et partager leurs moments préférés.

## Prérequis

- Docker et Docker Compose
- Git
- Un compte GitHub pour le déploiement
- Un serveur (par exemple, Digital Ocean) pour l'hébergement
- Une clé SSH pour le déploiement

## Installation Locale avec Docker

### 1. Cloner le projet

```bash
git clone https://github.com/ysh4me/projet-php.git
cd projet-php
```

### 2. Configuration de l'environnement

```bash
# Copier le fichier d'environnement
cp .env.example .env

# Configurer les variables d'environnement dans .env :
MYSQL_DATABASE=db_buddy_shotz
MYSQL_ROOT_PASSWORD=root
MYSQL_USER=app_user
MYSQL_PASSWORD=app_password
DB_HOST=db
DB_NAME=db_buddy_shotz
```

### 3. Lancer l'application

```bash
# Construire et démarrer les conteneurs
docker-compose up -d

# Installer les dépendances
docker-compose exec app composer install

# Donner les permissions nécessaires
docker-compose exec app chmod -R 777 storage/
```

L'application sera accessible à l'adresse : http://localhost:8080

## Déploiement sur un Serveur

### 1. Prérequis Serveur

- Un droplet Digital Ocean avec Ubuntu
- Docker et Docker Compose installés
- Votre clé SSH configurée

### 2. Configuration du Serveur

```bash
# Installation de Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Installation de Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.24.5/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

### 3. Déploiement

```bash
# Cloner le projet
git clone git@github.com:ysh4me/projet-php.git
cd projet-php

# Configuration de l'environnement de production
cp .env.production .env

# Déployer l'application
chmod +x deploy.sh
./deploy.sh
```

## Fonctionnalités Principales

### Gestion des Photos
- Upload de photos
- Création de groupe 
- Organisation des photos par albums
- Visualisation en mode galerie

### Gestion des Utilisateurs
- Inscription/Connexion
- Gestion du profil
- Récupération de mot de passe
- Envoie de mail de vérification

### Partage et Collaboration
- Partage d'albums
- Gestion des permissions

## Contribuer au Projet

### 1. Fork et Clone
- Fork le projet sur GitHub
- Clonez votre fork localement

### 2. Branches
- Créez une branche pour chaque fonctionnalité
- Nommez vos branches de manière descriptive 

### 3. Commits
- Faites des commits atomiques
- Utilisez des messages de commit clairs et descriptifs

### 4. Pull Requests
- Créez une Pull Request vers la branche main
- Décrivez clairement les changements apportés
- Attendez la review des mainteneurs

## Structure du Projet

```
projet-php/
├── app/
│   ├── Controllers/
│   ├── Models/
│   └── Views/
├── public/
│   ├── assets/
│   └── index.php
├── docker/
│   ├── Dockerfile
│   ├── nginx/
│   └── mysql/
├── docker-compose.yml
├── docker-compose.prod.yml
└── README.md
```

## Maintenance

### Mise à jour
```bash
# Mettre à jour les dépendances
composer update

# Reconstruire les conteneurs
docker-compose build
docker-compose up -d
```

### Logs et Debugging
```bash
# Voir les logs des conteneurs
docker-compose logs

# Accéder au shell du conteneur PHP
docker-compose exec app bash
```

## Support

Pour toute question ou problème :
1. Consultez les [Issues GitHub](https://github.com/ysh4me/projet-php/issues)
2. Créez une nouvelle issue si nécessaire
3. Contactez l'équipe de développement
