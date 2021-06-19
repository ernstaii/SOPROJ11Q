#!/bin/bash

cd ../ 2>&1 >/dev/null
printf "Installing composer libraries... \n\n"
sleep .5
composer install 2>&1 >/dev/null
printf "\nCreating a new .env file based on .env.example... \n\n"
sleep .5
cp .env.example .env 2>&1 >/dev/null
printf "\nGenerating the Laravel app key... \n\n"
sleep .5
php artisan key:generate 2>&1 >/dev/null
printf "\nDone. Project installed!"
sleep 1.5