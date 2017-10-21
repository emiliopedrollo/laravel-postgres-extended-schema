Laravel Postgres Extended
=========================

[![Build Status](https://travis-ci.org/bosnadev/database.svg?branch=master)](https://travis-ci.org/bosnadev/database)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/405f5153-4312-4c11-b0ae-8f27e2910c19/mini.png)](https://insight.sensiolabs.com/projects/405f5153-4312-4c11-b0ae-8f27e2910c19)
[![Code Climate](https://codeclimate.com/github/bosnadev/database/badges/gpa.svg)](https://codeclimate.com/github/bosnadev/database)
[![Latest Stable Version](https://poser.pugx.org/emiliopedrollo/laravel-postgres-extended-schema/v/stable)](https://packagist.org/packages/emiliopedrollo/laravel-postgres-extended-schema)
[![Total Downloads](https://poser.pugx.org/emiliopedrollo/laravel-postgres-extended-schema/downloads)](https://packagist.org/packages/emiliopedrollo/laravel-postgres-extended-schema)
[![Monthly Downloads](https://poser.pugx.org/emiliopedrollo/laravel-postgres-extended-schema/d/monthly)](https://packagist.org/packages/emiliopedrollo/laravel-postgres-extended-schema)
[![License](https://poser.pugx.org/emiliopedrollo/laravel-postgres-extended-schema/license)](https://packagist.org/packages/emiliopedrollo/laravel-postgres-extended-schema)


An extended PostgreSQL driver for Laravel 5.2+ with support for some aditional PostgreSQL data types: hstore, uuid, geometric types (point, path, circle, line, polygon...)

## Getting Started  
### Laravel 5.2+
1. Run `composer require emiliopedrollo/laravel-postgres-extended-schema` in your project root directory.
2. Add `Pedrollo\Database\DatabaseServiceProvider::class` to `config/app.php`'s `providers` array.

Then you are done.

### Lumen 5.*
1. Run `composer require emiliopedrollo/laravel-postgres-extended-schema` in your project root directory.
2. Add `$app->register(Pedrollo\Database\DatabaseServiceProvider::class);` to `bootstrap/app.php` (under the "Register Service Providers" section)
3. Uncomment the line `$app->withEloquent();` in `bootstrap/app.php`
