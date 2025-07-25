<?php

namespace Pedrollo\Database\Schema;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use Pedrollo\Database\IndexDefinition;
use Pedrollo\Database\Schema\Grammars\PostgresGrammar;

/**
 * Class Blueprint
 * @package Pedrollo\Database\Schema
 * @method IndexDefinition unique($columns, $name = null, $algorithm = null)
 * @method IndexDefinition index($columns, $name = null, $algorithm = null)
 * @method IndexDefinition primary($columns, $name = null, $algorithm = null)
 * @method IndexDefinition fullText($columns, $name = null, $algorithm = null)
 * @method IndexDefinition spatialIndex($columns, $name = null)
 * @method IndexDefinition rawIndex($expression, $name)
 */
class Blueprint extends \Illuminate\Database\Schema\Blueprint
{
    /**
     * Inherited table name
     * @var string|null
     */
    public $inherits;

    /** @var string  */
    public $partition_type = 'hash';

    /**
     * @var string|Expression|null
     */
    public $partition_expressions;

    /**
     * Specify table inheritance.
     *
     * @param  string $table
     * @return void
     */
    public function inherits($table)
    {
        $this->inherits = $table;
    }

    /**
     * @param string $type
     * @param array|string $expressions
     * @return void
     */
    public function partitionBy($type, $expressions)
    {
        $this->partition_type = $type;
        $this->partition_expressions = is_array($expressions) ? $expressions : [$expressions];
    }

    public function with($key, $value)
    {
        $this->commands[] = new Fluent(array_merge([
            'name' => 'with',
        ], compact('key','value')));
    }

    /**
     * Add the index commands fluently specified on columns.
     *
     * @return void
     */
    protected function addFluentIndexes(): void
    {
        foreach ($this->columns as $column) {
            foreach (array('primary', 'unique', 'index', 'gin', 'gist') as $index) {
                // If the index has been specified on the given column, but is simply
                // equal to "true" (boolean), no name has been specified for this
                // index, so we will simply call the index methods without one.
                if ($column->$index === true) {
                    $this->$index($column->name);

                    continue 2;
                }

                // If the index has been specified on the column and it is something
                // other than boolean true, we will assume a name was provided on
                // the index specification, and pass in the name to the method.
                elseif (isset($column->$index)) {
                    $this->$index($column->name, $column->$index);

                    continue 2;
                }
            }
        }
    }

    /**
     * Add a new index command to the blueprint.
     *
     * @param  string  $type
     * @param  string|array  $columns
     * @param  string  $index
     * @param  string|null  $algorithm
     * @return \Pedrollo\Database\IndexDefinition
     */
    protected function indexCommand($type, $columns, $index, $algorithm = null, $operatorClass = null)
    {
        $columns = (array) $columns;

        // If no name was specified for this index, we will create one using a basic
        // convention of the table name, followed by the columns, followed by an
        // index type, such as primary or index, which makes the index unique.
        $index = $index ?: $this->createIndexName($type, $columns);

        $unique = stripos($type, 'unique') !== false;
        if ($unique) $type = Str::camel(join('_',array_map(
            function($part) { return strtolower(str_ireplace('unique', 'index', $part)); },
            explode('_', Str::snake($type))
        )));

        return $this->addIndex(
            $type, compact('index', 'columns', 'algorithm', 'unique', 'operatorClass')
        );
    }

    protected function addIndex($name, array $parameters = [])
    {
        $this->commands[] = $command = $this->createIndex($name, $parameters);

        return $command;
    }

    protected function createIndex($name, array $parameters = [])
    {
        return new IndexDefinition(array_merge(compact('name'), $parameters));
    }

    /**
     * Specify an index for the table.
     *
     * @param  string|array  $columns
     * @param  string  $name
     * @return \Illuminate\Support\Fluent
     */
    public function gin($columns, $name = null, $where = null)
    {
        return $this->indexCommand('index', $columns, $name, 'gin');
    }

    /**
     * Specify a gist index for the table.
     *
     * @param  string|array  $columns
     * @param  string  $name
     * @return \Illuminate\Support\Fluent
     */
    public function gist($columns, $name = null)
    {
        return $this->indexCommand('index', $columns, $name, 'gist');
    }

    /**
     * Create a new character column on the table.
     *
     * @param  string $column
     * @param  int $length
     * @return \Illuminate\Support\Fluent
     */
    public function character($column, $length = 255)
    {
        return $this->addColumn('character', $column, compact('length'));
    }

    /**
     * @param $column
     * @return \Illuminate\Support\Fluent
     */
    public function hstore($column)
    {
        return $this->addColumn('hstore', $column);
    }

    /**
     * Create a new netmask (CIDR-notation) (cidr) column on the table.
     *
     * @param  string  $column
     * @return \Illuminate\Support\Fluent
     */
    public function netmask($column)
    {
        return $this->addColumn('netmask', $column);
    }

    /**
     * Create a new line column on the table.
     *
     * @param  string  $column
     * @return \Illuminate\Support\Fluent
     */
    public function line($column)
    {
        return $this->addColumn('line', $column);
    }

    /**
     * Create a new line segment (lseg) column on the table.
     *
     * @param  string  $column
     * @return \Illuminate\Support\Fluent
     */
    public function lineSegment($column)
    {
        return $this->addColumn('lineSegment', $column);
    }

    /**
     * Create a new path column on the table.
     *
     * @param  string  $column
     * @return \Illuminate\Support\Fluent
     */
    public function path($column)
    {
        return $this->addColumn('path', $column);
    }

    /**
     * Create a new box column on the table.
     *
     * @param  string  $column
     * @return \Illuminate\Support\Fluent
     */
    public function box($column)
    {
        return $this->addColumn('box', $column);
    }

    /**
     * Create a new circle column on the table.
     *
     * @param  string  $column
     * @return \Illuminate\Support\Fluent
     */
    public function circle($column)
    {
        return $this->addColumn('circle', $column);
    }

    /**
     * Create a new money column on the table.
     *
     * @param  string  $column
     * @return \Illuminate\Support\Fluent
     */
    public function money($column)
    {
        return $this->addColumn('money', $column);
    }

    /**
     * Create a new int4range column on the table.
     *
     * @param string $column
     *
     * @return \Illuminate\Support\Fluent
     */
    public function int4range($column)
    {
        return $this->addColumn('int4range', $column);
    }

    /**
     * Create a new int8range column on the table.
     *
     * @param string $column
     *
     * @return \Illuminate\Support\Fluent
     */
    public function int8range($column)
    {
        return $this->addColumn('int8range', $column);
    }

    /**
     * Create a new numrange column on the table.
     *
     * @param string $column
     *
     * @return \Illuminate\Support\Fluent
     */
    public function numrange($column)
    {
        return $this->addColumn('numrange', $column);
    }

    /**
     * Create a new tsrange column on the table.
     *
     * @param string $column
     *
     * @return \Illuminate\Support\Fluent
     */
    public function tsrange($column)
    {
        return $this->addColumn('tsrange', $column);
    }

    /**
     * Create a new tstzrange column on the table.
     *
     * @param string $column
     *
     * @return \Illuminate\Support\Fluent
     */
    public function tstzrange($column)
    {
        return $this->addColumn('tstzrange', $column);
    }

    /**
     * Create a new daterange column on the table.
     *
     * @param $column
     *
     * @return \Illuminate\Support\Fluent
     */
    public function daterange($column)
    {
        return $this->addColumn('daterange', $column);
    }

    /**
     * Create a new tsvector column on the table.
     *
     * @param $column
     *
     * @return \Illuminate\Support\Fluent
     */
    public function tsvector($column)
    {
        return $this->addColumn('tsvector', $column);
    }

    /**
     * Create a new bit column on the table.
     *
     * @param $column
     * @param int $length
     *
     * @return \Illuminate\Support\Fluent
     */
    public function bit($column, $length = 1)
    {
        return $this->addColumn('bit', $column, compact('length'));
    }

    /**
     * Create a new bytea column on the table.
     *
     * @param $column
     * @param int $length
     *
     * @return \Illuminate\Support\Fluent
     */
    public function bytea($column)
    {
        return $this->addColumn('bytea', $column);
    }

    /**
     * Create a new custom column on the table.
     *
     * @param $column
     * @param $custom
     * @return \Illuminate\Support\Fluent
     *
     */
    public function custom($column, $custom)
    {
        if (!PostgresGrammar::hasMacro('type' . ucfirst($custom))) {
            PostgresGrammar::macro('type' . ucfirst($custom),function (Fluent $column){
                return $column->type;
            });
        }
        return $this->addColumn($custom, $column);
    }
}
