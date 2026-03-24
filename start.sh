#!/bin/sh

#!/bin/sh

# Installer les dépendances prod
composer install

# Utilise le port fourni par Railway
PORT=${PORT:-8080}

# Lance le serveur PHP intégré
php -S 0.0.0.0:$PORT 