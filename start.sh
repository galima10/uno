#!/bin/sh
# Installe les dépendances PHP
composer install --no-dev --optimize-autoloader

# Installe les assets importmap
php bin/console importmap:install

# Utilise le port fourni par Railway
PORT=${PORT:-8080}

# Lance le serveur PHP intégré
php -S 0.0.0.0:$PORT public/router.php