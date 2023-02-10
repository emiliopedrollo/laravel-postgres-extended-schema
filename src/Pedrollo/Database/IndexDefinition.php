<?php

namespace Pedrollo\Database;

use Illuminate\Support\Fluent;

/**
 * @property mixed $unique
 * @property mixed $concurrently
 * @property mixed $index
 * @property mixed $algorithm
 * @property mixed $columns
 * @property mixed $distinctNulls
 * @property mixed $where
 */
class IndexDefinition extends Fluent
{
    public $name = 'index';



    public function __construct($attributes = [])
    {
        if (!isset($attributes['distinctNulls'])) {
            $attributes['distinctNulls'] = true;
        }
        parent::__construct($attributes);
    }

    /**
     * @param bool $unique
     * @return IndexDefinition
     */
    public function unique(bool $unique = true): IndexDefinition
    {
        $this->attributes['unique'] = $unique;
        return $this;
    }

    /**
     * @param bool|mixed $concurrently
     * @return IndexDefinition
     */
    public function concurrently(bool $concurrently = true): IndexDefinition
    {
        $this->attributes['concurrently'] = $concurrently;
        return $this;
    }

    /**
     * @param bool $distinctNulls
     * @return IndexDefinition
     */
    public function distinctNulls(bool $distinctNulls = true): IndexDefinition
    {
        $this->attributes['distinctNulls'] = $distinctNulls;
        return $this;
    }

    /**
     * @return IndexDefinition
     */
    public function dontDistinctNulls(): IndexDefinition
    {
        return $this->distinctNulls(false);
    }

    /**
     * @param string|null $where
     * @return IndexDefinition
     */
    public function where(?string $where): IndexDefinition
    {
        $this->attributes['where'] = $where;
        return $this;
    }

}
