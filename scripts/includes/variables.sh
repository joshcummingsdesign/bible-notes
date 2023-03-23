#!/usr/bin/env bash

# .env
if [ -f .env ]; then
    set -o allexport
    source .env
    set +o allexport
fi
