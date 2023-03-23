#!/usr/bin/env bash

rsync -aziP --delete wp-content/plugins/ wp/wp-content/plugins/
rsync -aziP --delete wp-content/themes/ wp/wp-content/themes/
