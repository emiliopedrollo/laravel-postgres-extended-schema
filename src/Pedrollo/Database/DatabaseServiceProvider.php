<?php

namespace Pedrollo\Database;

use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseServiceProvider as IlluminateServiceProvider;

/**
 * Class DatabaseServiceProvider
 * @package Pedrollo\Database
 */
class DatabaseServiceProvider extends IlluminateServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if (!Connection::getResolver('pgsql')) {
            Connection::resolverFor('pgsql', function ($connection, $database, $prefix, $config) {
                return new PostgresConnection($connection, $database, $prefix, $config);
            });
        }
    }
}
