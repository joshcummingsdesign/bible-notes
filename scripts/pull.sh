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
rm -rf wp-config-original.php
git checkout master wp-config.php
mv wp-config.php wp-config-original.php
rsync -aziP --delete wp-content/plugins/ ../wp-content/plugins/
rsync -aziP --delete wp-content/themes/ ../wp-content/themes/
printf "\nSuccess: Moved code.\n\n"

cd ../

printf "==> Pulling database...\n\n"
rm -rf db.sql
lando pull-db
printf "Success: Pulled database.\n\n"

printf "==> Importing database...\n\n"
lando wp db import db.sql

printf "\n==> Running search replace...\n\n"
lando wp search-replace $PROD_URL $LOCAL_URL --all-tables
mv wp/wp-config-original.php wp/wp-config.php

printf "\n==> Creating backup zip...\n\n"
zip -r bible-notes.zip db.sql wp-content

printf "\nSuccessfully pulled site!\n\n"
