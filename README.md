# Installer les dépendances avec Composer
composer install

# Créer le fichier .env et ajouter les lignes nécessaires
cat <<EOL > .env
MYSQL_DATABASE=db_buddy_shotz
MYSQL_ROOT_PASSWORD=root
MYSQL_USER=app_user
MYSQL_PASSWORD=app_password
DB_HOST=db
DB_NAME=db_buddy_shotz
EOL

# Construire et démarrer les conteneurs Docker
docker compose up --build -d

# Attendre que les conteneurs soient prêts
echo "Attente de 10 secondes pour que les conteneurs démarrent..."
sleep 10

# Exécuter la migration de la base de données
docker compose exec app php /var/www/html/scripts/migrate.php 0000_database-init.sql

# Prototype Figma
https://www.figma.com/proto/uGdWLv72H10dBncCMCl2yh/Untitled?node-id=1-3&starting-point-node-id=1%3A3
Vitepress : 

