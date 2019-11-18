<?php

namespace Pedrollo\Database;

use Doctrine\DBAL\Driver\PDOPgSql\Driver;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Grammar;
use Illuminate\Database\PostgresConnection as BasePostgresConnection;
use Illuminate\Database\Query\Grammars\PostgresGrammar;
use Illuminate\Database\Query\Processors\PostgresProcessor;

/**
 * Class PostgresConnection
 *
 * @package Pedrollo\Database
 */
class PostgresConnection extends BasePostgresConnection
{
    /**
     * Get a schema builder instance for the connection.
     *
     * @return Schema\Builder
     * @throws BindingResolutionException
     */
    public function getSchemaBuilder()
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return Container::getInstance()->make('db.connection.pgsql.builder',array($this));
    }

    /**
     * @return Grammar|PostgresGrammar
     * @throws BindingResolutionException
     */
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(Container::getInstance()->make('db.connection.pgsql.query.grammar'));
    }


    /**
     * Get the default schema grammar instance.
     *
     * @return Grammar
     * @throws BindingResolutionException
     */
    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(Container::getInstance()->make('db.connection.pgsql.schema.grammar'));
    }


    /**
     * Get the default post processor instance.
     *
     * @return PostgresProcessor
     * @throws BindingResolutionException
     */
    protected function getDefaultPostProcessor()
    {
        return Container::getInstance()->make('db.connection.pgsql.processor');
    }

    /**
     * @return Driver
     * @throws BindingResolutionException
     */
    protected function getDoctrineDriver()
    {
        return Container::getInstance()->make('db.connection.pgsql.driver');
    }
}
