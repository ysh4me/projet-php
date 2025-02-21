#!/bin/bash

# Arrêt des conteneurs en cours
docker-compose -f docker-compose.prod.yml down

# Pull des dernières modifications
git pull origin main

# Construction des images
docker-compose -f docker-compose.prod.yml build

# Démarrage des conteneurs
docker-compose -f docker-compose.prod.yml up -d

# Installation/Mise à jour des dépendances
docker-compose -f docker-compose.prod.yml exec app composer install --no-dev --optimize-autoloader

# Nettoyage du cache
docker-compose -f docker-compose.prod.yml exec app php artisan cache:clear
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache

# Migrations de la base de données
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

echo "Déploiement terminé avec succès !" 