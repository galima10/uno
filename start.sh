#!/bin/sh

#!/bin/sh

# Forcer l'environnement prod
export APP_ENV=prod
export APP_DEBUG=0

# Installer les dépendances prod
composer install --no-dev --optimize-autoloader

# Clear le cache prod
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# Utilise le port fourni par Railway
PORT=${PORT:-8080}

# Lance le serveur PHP intégré
php -S 0.0.0.0:$PORT public/index.php