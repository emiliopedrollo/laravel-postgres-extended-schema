<?php


namespace Pedrollo\Database\Query;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder as BaseEloquentBuilder;
use Illuminate\Database\Query\Builder as BaseBuilder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;

class Builder extends BaseBuilder
{
    /**
     * The common table expressions.
     *
     * @var array
     */
    public array $expressions = [];

    /**
     * Builder constructor.
     * @param Connection $connection
     * @param Grammar|null $grammar
     * @param Processor|null $processor
     */
    public function __construct(
        Connection $connection, ?Grammar $grammar = null, ?Processor $processor = null
    )
    {
        parent::__construct($connection, $grammar, $processor);

        $this->bindings = ['expressions' => []] + $this->bindings;
    }

    /**
     * Add a common table expression to the query.
     *
     * @param string $name
     * @param Closure|BaseEloquentBuilder|BaseBuilder|string $query
     * @param array|null $columns
     * @param bool $recursive
     * @return $this
     */
    public function withExpression(
        string $name, Closure|BaseEloquentBuilder|BaseBuilder|string $query,
        ?array $columns = null, bool $recursive = false
    ): static
    {
        [$query, $bindings] = $this->createSub($query);
        $this->expressions[] = compact('name', 'query', 'columns', 'recursive');
        $this->addBinding($bindings, 'expressions');
        return $this;
    }

    /**
     * Add a recursive common table expression to the query.
     *
     * @param string $name
     * @param Closure|BaseBuilder|string $query
     * @param array|null $columns
     * @return $this
     */
    public function withRecursiveExpression($name, $query, $columns = null)
    {
        return $this->withExpression($name, $query, $columns, true);
    }

    /**
     * Insert new records into the table using a subquery.
     *
     * @param array $columns
     * @param Closure|BaseBuilder|string $query
     * @return bool
     */
    public function insertUsing(array $columns, $query)
    {
        [$sql, $bindings] = $this->createSub($query);
        $bindings = array_merge($this->bindings['expressions'], $bindings);
        return $this->connection->affectingStatement(
            $this->grammar->compileInsertUsing($this, $columns, $sql),
            $this->cleanBindings($bindings)
        );
    }

    public function lateralJoin($query, string $as, string $type = 'inner'): static
    {
        return parent::joinLateral($query, $as, $type);
    }

    /** @deprecated Use Builder::lateralJoin() */
    public function crossJoinLateral($table, $first = null, $operator = null, $second = null)
    {
        return parent::crossJoin(new Expression('lateral '.$table), $first,$operator, $second);
    }

    /** @deprecated Use Builder::lateralJoin() */
    public function crossJoinLateralSub($query, $as)
    {
        [$query, $bindings] = $this->createSub($query);
        $expression = 'lateral ('.$query.') as '.$this->grammar->wrapTable($as);
        $this->addBinding($bindings, 'join');
        $this->joins[] = $this->newJoinClause($this, 'cross', new Expression($expression));
        return $this;
    }

    /** @deprecated Use Builder::lateralJoin() */
    public function joinLateral($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
    {
        return parent::join(new Expression('lateral '.$table), $first, $operator, $second, $type, $where);
    }

    /** @deprecated Use Builder::lateralJoin() */
    public function joinLateralSub($query, $as, $first, $operator = null, $second = null, $type = 'inner', $where = false)
    {
        [$query, $bindings] = $this->createSub($query);
        $expression = 'lateral ('.$query.') as '.$this->grammar->wrapTable($as);
        $this->addBinding($bindings, 'join');
        return $this->join(new Expression($expression), $first, $operator, $second, $type, $where);
    }

    /** @deprecated Use Builder::lateralJoin() */
    public function leftLateralJoin($table, $first, $operator = null, $second = null)
    {
        return parent::leftJoin(new Expression('lateral '.$table), $first, $operator, $second);
    }

    /** @deprecated Use Builder::lateralJoin() */
    public function leftLateralJoinSub($query, $as, $first, $operator = null, $second = null)
    {
        return $this->joinLateralSub($query, $as, $first, $operator, $second, 'left');
    }

    /** @deprecated Use Builder::lateralJoin() */
    public function rightLateralJoin($table, $first, $operator = null, $second = null)
    {
        return parent::rightJoin(new Expression('lateral '.$table), $first, $operator, $second);
    }

    /** @deprecated Use Builder::lateralJoin() */
    public function rightLateralJoinSub($query, $as, $first, $operator = null, $second = null)
    {
        return $this->joinLateralSub($query, $as, $first, $operator, $second, 'right');
    }


}
