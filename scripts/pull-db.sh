#!/usr/bin/env bash

source scripts/includes/variables.sh

mysqldump -u $DB_USER -p$DB_PASS -h $DB_HOST -P $DB_PORT $DB_NAME > db.sql
