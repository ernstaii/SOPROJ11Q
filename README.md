# Hunted
![master](https://img.shields.io/github/workflow/status/lderkzen/SOPROJ11Q/Laravel/dev?label=master)
![dev](https://img.shields.io/github/workflow/status/lderkzen/SOPROJ11Q/Laravel/dev?label=dev)
[![Issues](https://img.shields.io/github/issues/lderkzen/SOPROJ11Q)](https://github.com/lderkzen/SOPROJ11Q/issues)
[![Pull Requests](https://img.shields.io/github/issues-pr/lderkzen/SOPROJ11Q)](https://github.com/lderkzen/SOPROJ11Q/pulls)

## Features


## Configuration steps for contribution
- Clone the project.
- cd into your project folder.
- Run the following commands:
  - `composer install` | Install all dependencies
  - `cp .env.example .env` | Create a fresh .env file
  - `php artisan key:generate` | Generate an unique encryption key
- Fill the .env file with all necessary information
- (Opt.) If you're using a local database for testing purposes run the following commands:
  - `php artisan migrate:fresh` | Migrate the database
  - `php artisan db:seed` | Seed the database with test data

## Hosting the API and web portal

In order to run the api locally
  - `php artisan serve`

In order to run the api locally but allow access to other devices / emulators
  - `ipconfig`
  - Copy your ipv4 address
  - `php artisan serve --host <IPV4 ADDRESS>`


## Links
- [Wiki](https://github.com/lderkzen/SOPROJ11Q/wiki)
- [Taiga](https://tree.taiga.io/project/leonniekus-project-everyware-q/epics)


---
Made by: Group Q
