#!/bin/sh
# Récupère le port fourni par Railway
PORT=${PORT:-8080}

# Lance le serveur PHP intégré sur ce port
php -S 0.0.0.0:$PORT -t public