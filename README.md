# Portail de gestion - Commune d'Ahfir

Application Laravel 12 / Bootstrap 5 destinée à la gestion administrative de la Commune d'Ahfir.

## Fonctionnalités

- Authentification sécurisée et gestion des rôles `admin` / `agent`
- Gestion des citoyens, employés et services communaux
- Gestion et suivi des demandes administratives
- Notifications automatiques lors du dépôt et du changement d'état
- Tableau de bord avec statistiques et graphique Chart.js
- Validation serveur, messages d'erreur et contraintes relationnelles
- Interface responsive Bootstrap 5

## Prérequis XAMPP

- PHP 8.2 ou supérieur
- MySQL/MariaDB XAMPP sur `127.0.0.1:3307`
- Composer

Laravel 13 est la version stable la plus récente au 4 juin 2026, mais exige PHP 8.3. Ce projet utilise Laravel 12.61.1 afin de fonctionner immédiatement avec le PHP 8.2 de XAMPP installé.

## Installation

1. Démarrer Apache et MySQL depuis le panneau XAMPP.
2. Vérifier que MySQL utilise le port `3307` et que le compte `root` n'a pas de mot de passe.
3. Créer la base dans phpMyAdmin (`http://localhost/phpmyadmin`) :

```sql
CREATE DATABASE commune_ahfir CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

4. Ouvrir PowerShell dans `C:\xampp\htdocs\commune-ahfir`, puis exécuter :

```powershell
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

5. Ouvrir `http://127.0.0.1:8000`.

Pour utiliser directement Apache, configurer un VirtualHost dont le `DocumentRoot` pointe vers `C:\xampp\htdocs\commune-ahfir\public`.

## Connexion de démonstration

- Administrateur : `admin@ahfir.ma` / `password`
- Agent : `agent@ahfir.ma` / `password`

Changer ces mots de passe avant toute utilisation au-delà du développement local.

## Configuration MySQL

Le fichier `.env.example` est préconfiguré avec :

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=commune_ahfir
DB_USERNAME=root
DB_PASSWORD=
```

## Vérification

```powershell
php artisan test
php artisan route:list
php artisan view:cache
```
