#!/bin/bash

cd ../ 2>&1 >/dev/null
printf "Running fresh database migrations with seeders... \n\n"
sleep .5
php artisan migrate:fresh --seed 2>&1 >/dev/null
printf "\nOptimizing Laravel installation cache... \n\n"
sleep .5
php artisan optimize 2>&1 >/dev/null
printf "\nClearing config cache... \n\n"
sleep .5
php artisan config:clear 2>&1 >/dev/null
sleep .5
php artisan serve