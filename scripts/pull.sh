#!/usr/bin/env bash

set -e
trap "exit" INT
source scripts/includes/variables.sh

printf "===== Pulling Prod Site =====\n\n"

cd wp

printf "==> Pulling code...\n\n"
git pull
printf "\nSuccess: Pulled code.\n\n"

printf "==> Moving code...\n\n"
rm -rf wp-config.php
rsync -aziP --delete wp-content/plugins/ ../wp-content/plugins/
rsync -aziP --delete wp-content/themes/ ../wp-content/themes/
printf "\nSuccess: Moved code.\n\n"

cd ../

printf "==> Pulling database...\n\n"
lando pull-db
printf "Success: Pulled database.\n\n"

printf "==> Importing database...\n\n"
lando wp db import db.sql
rm -rf db.sql

printf "\n==> Running search replace...\n\n"
lando wp search-replace $PROD_URL $LOCAL_URL --all-tables

printf "\nSuccessfully pulled site!\n\n"
