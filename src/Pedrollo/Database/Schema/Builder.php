<?php

namespace Pedrollo\Database\Schema;

use Closure;

/**
 * Class Builder
 * @package Pedrollo\Database\Schema
 */
class Builder extends \Illuminate\Database\Schema\PostgresBuilder
{
    /**
     * Create a new command set with a Closure.
     *
     * @param string $table
     * @param Closure $callback
     * @return Blueprint
     */
    protected function createBlueprint($table, Closure $callback = null)
    {
        return new Blueprint($table, $callback);
    }
}