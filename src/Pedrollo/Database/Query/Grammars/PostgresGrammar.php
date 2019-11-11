<?php

namespace Pedrollo\Database\Query\Grammars;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\PostgresGrammar as LaravelPostgresGrammar;

/**
 * Class PostgresGrammar
 *
 * @package Pedrollo\Database\Query\Grammars
 */
class PostgresGrammar extends LaravelPostgresGrammar
{
    /**
     * @var array
     */
    protected $jsonOperators = [
        '->',
        '->>',
        '#>',
        '#>>',
    ];

    /**
     * @param string $value
     *
     * @param bool $prefixAlias
     * @return string
     */
    public function wrap($value, $prefixAlias = false)
    {
        if ($value === '*') {
            return $value;
        }

        // If querying hstore
        /** @noinspection RegExpRedundantEscape */
        if (preg_match('/\[(.*?)\]/', $value, $match)) {
            return (string)str_replace(array('[', ']'), '', $match[1]);
        }

        // If querying json column
        foreach ($this->jsonOperators as $operator) {
            if (stripos($value, $operator)) {
                list($value, $key) = explode($operator, $value, 2);
                return parent::wrap($value, $prefixAlias) . $operator . $key;
            }
        }

        return parent::wrap($value, $prefixAlias);
    }

    /**
     * Compile a "where null" clause.
     *
     * @param  Builder $query
     * @param  array   $where
     *
     * @return string
     */
    protected function whereNull(Builder $query, $where)
    {
        return '(' . $this->wrap($where['column']) . ') is null';
    }

    /**
     * Compile a "where not null" clause.
     *
     * @param  Builder $query
     * @param  array   $where
     *
     * @return string
     */
    protected function whereNotNull(Builder $query, $where)
    {
        return '(' . $this->wrap($where['column']) . ') is not null';
    }


    /**
     * Get the format for database stored dates.
     *
     * @return string
     */
    public function getDateFormat()
    {
        return 'Y-m-d H:i:sO';
    }
}
