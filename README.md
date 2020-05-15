Laravel Postgres Extended
=========================

[![Build Status](https://travis-ci.org/emiliopedrollo/laravel-postgres-extended-schema.svg?branch=master)](https://travis-ci.org/emiliopedrollo/laravel-postgres-extended-schema)
[![Maintainability](https://api.codeclimate.com/v1/badges/5c06fa52e00dfd5d05d0/maintainability)](https://codeclimate.com/github/emiliopedrollo/laravel-postgres-extended-schema/maintainability)
[![Latest Stable Version](https://poser.pugx.org/emiliopedrollo/laravel-postgres-extended-schema/v/stable)](https://packagist.org/packages/emiliopedrollo/laravel-postgres-extended-schema)
[![Total Downloads](https://poser.pugx.org/emiliopedrollo/laravel-postgres-extended-schema/downloads)](https://packagist.org/packages/emiliopedrollo/laravel-postgres-extended-schema)
[![Monthly Downloads](https://poser.pugx.org/emiliopedrollo/laravel-postgres-extended-schema/d/monthly)](https://packagist.org/packages/emiliopedrollo/laravel-postgres-extended-schema)
[![License](https://poser.pugx.org/emiliopedrollo/laravel-postgres-extended-schema/license)](https://packagist.org/packages/emiliopedrollo/laravel-postgres-extended-schema)

## Introduction

An extended PostgreSQL driver for Laravel 6.0+ with support for some aditional PostgreSQL data types: hstore, uuid, geometric types (point, path, circle, line, polygon...) and support for WITH \[RECURSIVE\] clause

## Installation  
### Laravel 6.0+
Simple run `composer require emiliopedrollo/laravel-postgres-extended-schema` in your project root directory.

Then you are done.

### Lumen 6.*
1. Run `composer require emiliopedrollo/laravel-postgres-extended-schema` in your project root directory.
2. Uncomment the line `$app->withEloquent();` in `bootstrap/app.php`
