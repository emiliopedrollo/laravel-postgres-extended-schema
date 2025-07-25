<?php

namespace Pedrollo\Database\Schema\Grammars;

use Illuminate\Database\Query\Expression;
use Illuminate\Support\Fluent;
use Illuminate\Database\Schema\Blueprint as BaseBlueprint;

/**
 * Class PostgresGrammar
 * @package Pedrollo\Database\Schema\Grammars
 */
class PostgresGrammar extends \Illuminate\Database\Schema\Grammars\PostgresGrammar
{


    /**
     * Create the column definition for a character type.
     *
     * @param Fluent $column
     * @return string
     */
    protected function typeCharacter(Fluent $column)
    {
        return "character({$column->length})";
    }

    /**
     * Create the column definition for a hstore type.
     *
     * @param Fluent $column
     * @return string
     */
    protected function typeHstore(Fluent $column)
    {
        return "hstore";
    }

    /**
     * Create the column definition for a uuid type.
     *
     * @param Fluent $column
     * @return string
     */
    protected function typeUuid(Fluent $column)
    {
        return "uuid";
    }

    /**
     * Create the column definition for a jsonb type.
     *
     * @param Fluent $column
     * @return string
     */
    protected function typeJsonb(Fluent $column)
    {
        return "jsonb";
    }

    /**
     * Create the column definition for an IPv4 or IPv6 address.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeIpAddress(Fluent $column)
    {
        return 'inet';
    }
    /**
     * Create the column definition for a CIDR notation-style netmask.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeNetmask(Fluent $column)
    {
        return 'cidr';
    }

    /**
     * Create the column definition for a MAC address.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeMacAddress(Fluent $column)
    {
        return 'macaddr';
    }

    /**
     * Create the column definition for a 2D geometric point (x, y), x and y are floating-point numbers.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typePoint(Fluent $column)
    {
        return 'point';
    }

    /**
     * Create the column definition for a line represented as a standard linear equation Ax + By + C = 0.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeLine(Fluent $column)
    {
        return 'line';
    }

    /**
     * Create the column definition for a line segment represented by two points (x1, y1), (x2, y2).
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeLineSegment(Fluent $column)
    {
        return 'lseg';
    }

    /**
     * Create the column definition for a path represented as a list of points (x1, y1), (x2, y2), ..., (xn, yn).
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typePath(Fluent $column)
    {
        return 'path';
    }

    /**
     * Create the column definition for a box represented by opposite corners of the box as points (x1, y1), (x2, y2).
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeBox(Fluent $column)
    {
        return 'box';
    }

    /**
     * Create the column definition for a polygon represented by a list of points (vertices of the polygon).
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typePolygon(Fluent $column)
    {
        return 'polygon';
    }

    /**
     * Create the column definition for a circle represented by a center point and a radius <(x, y), r>
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeCircle(Fluent $column)
    {
        return 'circle';
    }

    /**
     * Create the column definition for storing amounts of currency with a fixed fractional precision.
     *
     * This will store values in the range of: -92233720368547758.08 to +92233720368547758.07. (92 quadrillion).
     * Output is locale-sensitive, see lc_monetary setting of PostgreSQL instance/s.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeMoney(Fluent $column)
    {
        return 'money';
    }

    /**
     * Create the column definition for an int4range type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeInt4range(Fluent $column)
    {
        return "int4range";
    }

    /**
     * Create the column definition for an int8range type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeInt8range(Fluent $column)
    {
        return "int8range";
    }

    /**
     * Create the column definition for an numrange type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeNumrange(Fluent $column)
    {
        return "numrange";
    }

    /**
     * Create the column definition for an tsrange type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeTsrange(Fluent $column)
    {
        return "tsrange";
    }

    /**
     * Create the column definition for an tstzrange type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeTstzrange(Fluent $column)
    {
        return "tstzrange";
    }

    /**
     * Create the column definition for an daterange type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeDaterange(Fluent $column)
    {
        return "daterange";
    }

    /**
     * Create the column definition for a Text Search Vector type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeTsvector(Fluent $column)
    {
        return "tsvector";
    }

    /**
     * Create the column definition for a Bytea type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeBytea(Fluent $column)
    {
        return "bytea";
    }

    /**
     * @param mixed $value
     * @return mixed|string
     */
    protected function getDefaultValue($value)
    {
        if($this->isUuidGenerator($value)) return strval($value);

        return parent::getDefaultValue($value);
    }

    /**
     * Check if string matches on of uuid_generate functions
     *
     * @param mixed $value
     * @return bool
     */
    protected function isUuidGenerator(mixed $value)
    {
        return is_string($value) && str_starts_with($value, 'uuid_generate_v');
    }

    public function compileWith(BaseBlueprint $blueprint, Fluent $command){
        if (is_string($command->value)) {
            $value = $this->wrap($command->value);
        } elseif (is_bool($command->value)) {
            $value = ($command->value?'true':'false');
        } else $value = ((string) $command->value);

        return sprintf('alter table %s set (%s = %s)',
            $this->wrapTable($blueprint),
            $command->key,
            $value
        );
    }

    /**
     * Compile create table query.
     *
     * @param BaseBlueprint $blueprint
     * @param  \Illuminate\Support\Fluent $command
     * @return array|string
     */
    public function compileCreate(BaseBlueprint $blueprint, Fluent $command)
    {
        $sql = parent::compileCreate($blueprint, $command);

        $addToSql = function($string) use (&$sql) {
            (is_string($sql) ? $sql .= $string : $sql[0] .= $string);
        };

        if (isset($blueprint->inherits)) {
            $addToSql(' inherits ("'.$blueprint->inherits.'")');
        }
        if (isset($blueprint->partition_expressions)) {

            $expressions = join(', ',array_map(function ($expression) {
                return $expression instanceof Expression
                    ? $expression->getValue($this)
                    : '"'.$expression.'"';
            },$blueprint->partition_expressions));

            $addToSql(' partition by '.$blueprint->partition_type.' ('.$expressions.')');
        }
        return $sql;
    }

    /**
     * Create the column definition for a bit type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeBit(Fluent $column)
    {
        return "bit({$column->length})";
    }

    /**
     * Compile a unique key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileUnique(BaseBlueprint $blueprint, Fluent $command)
    {
        return sprintf( 'create unique index %s on %s (%s) %s',
            $this->wrap($command->index),
            $this->wrapTable($blueprint),
            $this->columnize($command->columns),
            isset($command->algorithm)?$command->algorithm:''
        );
    }

    /**
     * Compile a plain index key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Pedrollo\Database\IndexDefinition  $command
     * @return string
     */
    public function compileIndex(BaseBlueprint $blueprint, Fluent $command)
    {
        return preg_replace('/\s+/',' ',
            sprintf('create %s index %s %s on %s %s (%s) %s %s',
                $command->unique ? 'unique' : '',
                $command->concurrently ? 'concurrently' : '',
                $this->wrap($command->index),
                $this->wrapTable($blueprint),
                $command->algorithm ? 'using '.$command->algorithm : '',
                $this->columnize($command->columns),
                $command->distinctNulls ? '' : 'nulls not distinct',
                $command->where ? 'where '.$command->where : ''
            )
        );
    }
}
