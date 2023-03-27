Laravel Postgres Extended
=========================

[![Build Status](https://travis-ci.org/emiliopedrollo/laravel-postgres-extended-schema.svg?branch=master)](https://travis-ci.org/emiliopedrollo/laravel-postgres-extended-schema)
[![Maintainability](https://api.codeclimate.com/v1/badges/5c06fa52e00dfd5d05d0/maintainability)](https://codeclimate.com/github/emiliopedrollo/laravel-postgres-extended-schema/maintainability)
[![Latest Stable Version](https://poser.pugx.org/emiliopedrollo/laravel-postgres-extended-schema/v/stable)](https://packagist.org/packages/emiliopedrollo/laravel-postgres-extended-schema)
[![Total Downloads](https://poser.pugx.org/emiliopedrollo/laravel-postgres-extended-schema/downloads)](https://packagist.org/packages/emiliopedrollo/laravel-postgres-extended-schema)
[![Monthly Downloads](https://poser.pugx.org/emiliopedrollo/laravel-postgres-extended-schema/d/monthly)](https://packagist.org/packages/emiliopedrollo/laravel-postgres-extended-schema)
[![License](https://poser.pugx.org/emiliopedrollo/laravel-postgres-extended-schema/license)](https://packagist.org/packages/emiliopedrollo/laravel-postgres-extended-schema)
[![License](https://poser.pugx.org/emiliopedrollo/laravel-postgres-extended-schema/license)](https://packagist.org/packages/emiliopedrollo/laravel-postgres-extended-schema)

## Introduction

An extended PostgreSQL driver for Laravel 9+ with support for some aditional PostgreSQL data types: hstore, uuid, geometric types (point, path, circle, line, polygon...) and support for WITH \[RECURSIVE\] clause

## Installation  

Simple run `composer require emiliopedrollo/laravel-postgres-extended-schema` in your project root directory.

Then you are done.

## Usage

- [SELECT Queries](#select-queries)
- [INSERT/UPDATE/DELETE Queries](#insertupdatedelete-queries)
- [Eloquent](#eloquent)
  - [Recursive Relationships](#recursive-relationships)

### SELECT Queries

Use `withExpression()` and provide a query builder instance, an SQL string or a closure:

```php
$posts = DB::table('p')
    ->select('p.*', 'u.name')
    ->withExpression('p', DB::table('posts'))
    ->withExpression('u', function ($query) {
        $query->from('users');
    })
    ->join('u', 'u.id', '=', 'p.user_id')
    ->get();
```

Use `withRecursiveExpression()` for recursive expressions:

```php
$query = DB::table('users')
    ->whereNull('parent_id')
    ->unionAll(
        DB::table('users')
            ->select('users.*')
            ->join('tree', 'tree.id', '=', 'users.parent_id')
    );

$tree = DB::table('tree')
    ->withRecursiveExpression('tree', $query)
    ->get();
```

You can provide the expression's columns as the third argument:

```php
$query = 'select 1 union all select number + 1 from numbers where number < 10';

$numbers = DB::table('numbers')
    ->withRecursiveExpression('numbers', $query, ['number'])
    ->get();
```

### INSERT/UPDATE/DELETE Queries

You can use common table expressions in `INSERT`, `UPDATE` and `DELETE` queries:

```php
DB::table('profiles')
    ->withExpression('u', DB::table('users')->select('id', 'name'))
    ->insertUsing(['user_id', 'name'], DB::table('u'));
```

```php
DB::table('profiles')
    ->withExpression('u', DB::table('users'))
    ->join('u', 'u.id', '=', 'profiles.user_id')
    ->update(['profiles.name' => DB::raw('u.name')]);
```

```php
DB::table('profiles')
    ->withExpression('u', DB::table('users')->where('active', false))
    ->whereIn('user_id', DB::table('u')->select('id'))
    ->delete();
```

### Eloquent

You can use common table expressions in Eloquent queries.

```php
$query = User::whereNull('parent_id')
    ->unionAll(
        User::select('users.*')
            ->join('tree', 'tree.id', '=', 'users.parent_id')
    );

$tree = User::from('tree')
    ->withRecursiveExpression('tree', $query)
    ->get();
```

#### Recursive Relationships

If you want to implement recursive relationships, you can use this package: [staudenmeir/laravel-adjacency-list](https://github.com/staudenmeir/laravel-adjacency-list)
