#!/bin/sh

# Installer les dépendances PHP
composer install --no-interaction

# Forcer l'environnement (dev pour l'instant)
export APP_ENV=dev
export APP_DEBUG=1

# Utiliser le port fourni par Railway
PORT=${PORT:-8080}

# Lancer le serveur PHP intégré en pointant vers public/
# php -S 0.0.0.0:$PORT -t public public/index.php
cd public
php -S 0.0.0.0:$PORT router.php