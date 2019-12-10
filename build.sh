#!/bin/bash
FILE=./var/data.sqlite
if [ ! -f "$FILE" ]; then
    echo "create DB"
    docker run -u $UID:$UID -v var:/app -w /app nouchka/sqlite3 data.sqlite
fi

composer install
./vendor/bin/codecept build

./bin/console assets:install
./bin/console doctrine:schema:drop -f
./bin/console doctrine:schema:create
./bin/console doctrine:migrations:migrate prev -n
./bin/console doctrine:migrations:migrate -n
./bin/console app:currency-rate-update
