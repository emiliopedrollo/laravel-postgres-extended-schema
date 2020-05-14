<?php


namespace Pedrollo\Database\Query\Grammars;

use Illuminate\Database\Query\Builder as BaseBuilder;
use Pedrollo\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\PostgresGrammar as BasePostgresGrammar;

class PostgresGrammar extends BasePostgresGrammar
{
    /**
     * Create a new grammar instance.
     */
    public function __construct()
    {
        array_unshift($this->selectComponents, 'expressions');
    }
    /**
     * Compile the common table expressions.
     *
     * @param Builder|BaseBuilder $query
     * @return string
     */
    public function compileExpressions(BaseBuilder $query)
    {
        if (!$query->expressions) {
            return '';
        }

        $recursive = $this->recursiveKeyword($query->expressions);

        $statements = [];

        foreach ($query->expressions as $expression) {
            $columns = $expression['columns'] ? '('.$this->columnize($expression['columns']).') ' : '';

            $statements[] = $this->wrapTable($expression['name']).' '.$columns.'as ('.$expression['query'].')';
        }

        return 'with '.$recursive.implode(', ', $statements);
    }

    /**
     * Get the "recursive" keyword.
     *
     * @param array $expressions
     * @return string
     */
    protected function recursiveKeyword(array $expressions)
    {
        return collect($expressions)->where('recursive', true)->isNotEmpty() ? 'recursive ' : '';
    }

    /**
     * Compile an insert statement using a subquery into SQL.
     *
     * @param Builder|BaseBuilder $query
     * @param array $columns
     * @param string $sql
     * @return string
     */
    public function compileInsertUsing(BaseBuilder $query, array $columns, string $sql)
    {
        $expressions = $this->compileExpressions($query);

        $compiled = parent::compileInsertUsing($query, $columns, $sql);

        return trim("$expressions $compiled");
    }

    /**
     * Compile an update statement into SQL.
     *
     * @param Builder|BaseBuilder $query
     * @param array $values
     * @return string
     */
    public function compileUpdate(BaseBuilder $query, array $values)
    {
        $compiled = parent::compileUpdate($query, $values);

        $expressions = $this->compileExpressions($query);

        return trim("$expressions $compiled");
    }

    /**
     * Prepare the bindings for an update statement.
     *
     * @param array $bindings
     * @param array $values
     * @return array
     */
    public function prepareBindingsForUpdate(array $bindings, array $values)
    {
        $values = array_merge($bindings['expressions'], $values);

        unset($bindings['expressions']);

        return parent::prepareBindingsForUpdate($bindings, $values);
    }

    /**
     * Compile a delete statement into SQL.
     *
     * @param Builder|BaseBuilder $query
     * @return string
     */
    public function compileDelete(BaseBuilder $query)
    {
        $compiled = parent::compileDelete($query);

        $expressions = $this->compileExpressions($query);

        return trim("$expressions $compiled");
    }

}
