#!/bin/bash

FILE=./var/data.sqlite
if [ ! -f "$FILE" ]; then
    echo "create DB"
    docker run -u $UID -v $PWD/var:/app -w /app nouchka/sqlite3 data.sqlite
fi

composer install
./bin/console doctrine:schema:create
./bin/console doctrine:migrations:migrate -n
./bin/console assets:install
./vendor/bin/codecept build
