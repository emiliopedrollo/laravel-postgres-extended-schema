<?php

namespace Pedrollo\Database;

use Illuminate\Database\Grammar;
use Illuminate\Database\PostgresConnection as BasePostgresConnection;
use Illuminate\Database\Query\Grammars\Grammar as QueryGrammar;
use Illuminate\Database\Schema\PostgresBuilder;
use Illuminate\Support\Facades\DB;
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
    protected function getDefaultSchemaGrammar(): Grammar
    {
        return new Schema\Grammars\PostgresGrammar($this);
    }

    protected function getDefaultQueryGrammar(): QueryGrammar
    {
        return new Query\Grammars\PostgresGrammar($this);
    }
}
