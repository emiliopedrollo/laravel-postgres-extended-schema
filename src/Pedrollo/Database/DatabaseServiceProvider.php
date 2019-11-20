<?php

namespace Pedrollo\Database;

use Closure;
use Doctrine\DBAL\Driver\PDOPgSql\Driver as DoctrineDriver;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\DatabaseServiceProvider as IlluminateServiceProvider;
use Illuminate\Database\Query\Grammars\PostgresGrammar;
use Illuminate\Database\Query\Processors\PostgresProcessor;
use Pedrollo\Database\Connectors\ConnectionFactory;
use Pedrollo\Database\Schema\Builder;

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
        // The connection factory is used to create the actual connection instances on
        // the database. We will inject the factory into the manager so that it may
        // make the connections while they are actually needed and not of before.
        $this->app->singleton('db.factory', function ($app) {
            return new ConnectionFactory($app);
        });

        // The database manager is used to resolve various connections, since multiple
        // connections might be managed. It also implements the connection resolver
        // interface which may be used by other components requiring connections.
        $this->app->singleton('db', function ($app) {
            return new DatabaseManager($app, $app['db.factory']);
        });

        // The postgres connection, provided here to facilitate extensibility, by default
        // the extended version is used.
        $this->app->singleton('db.connection.pgsql',
            function(/** @noinspection PhpUnusedParameterInspection */ $app, $params) {

                /**
                 * @var Closure $connection
                 * @var string $database
                 * @var string|null $prefix
                 * @var array|null $config
                 */
                extract($params,EXTR_SKIP);

                return new PostgresConnection($connection, $database, $prefix ?? '', $config ?? []);
            });

        // The scheme builder, provided here to facilitate extensibility, by default
        // the illuminate base is used.
        $this->app->singleton('db.connection.pgsql.builder',
            function(/** @noinspection PhpUnusedParameterInspection */ $app, $params) {
                return new Builder($params['connection']);
            });

        // The query grammar, provided here to facilitate extensibility, by default
        // the illuminate base is used.
        $this->app->singleton('db.connection.pgsql.query.grammar',PostgresGrammar::class);

        // The Schema Grammar, provided here do facilitate extensibility, by default
        // the extended version is used.
        $this->app->singleton('db.connection.pgsql.schema.grammar', Schema\Grammars\PostgresGrammar::class);

        // The post processor, provided here to facilitate extensibility, by default
        // the illuminate base is used.
        $this->app->singleton('db.connection.pgsql.processor',PostgresProcessor::class);

        // The doctrine driver, provided here to facilitate extensibility, by default
        // the default driver from doctrine is used.
        $this->app->singleton('db.connection.pgsql.driver',DoctrineDriver::class);
    }
}
