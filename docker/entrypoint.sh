#!/bin/sh
set -e

echo "Initializing application..."

# Do any necessary setup here, such as running migrations or seeding the database
php vendor/bin/phinx migrate --configuration=phinx.php

exec "$@"