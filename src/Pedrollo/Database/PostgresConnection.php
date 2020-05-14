<?php

namespace Pedrollo\Database;

use Illuminate\Database\Grammar;
use Illuminate\Database\PostgresConnection as BasePostgresConnection;
use Illuminate\Database\Schema\PostgresBuilder;
use Pedrollo\Database\Query\Builder;
use Pedrollo\Database\Schema\Blueprint;

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
     * @return PostgresBuilder
     */
    public function getSchemaBuilder()
    {
        $builder =  parent::getSchemaBuilder();
        $builder->blueprintResolver(function($table, $callback, $prefix){
            return new Blueprint($table, $callback, $prefix);
        });
        return $builder;
    }

    public function query()
    {
        return new Builder($this, $this->getQueryGrammar(), $this->getPostProcessor());
    }


    /**
     * Get the default schema grammar instance.
     *
     * @return Grammar
     */
    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(new Schema\Grammars\PostgresGrammar());
    }

    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new Query\Grammars\PostgresGrammar());
    }
}
