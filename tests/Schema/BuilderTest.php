<?php

namespace Tests\Schema;

use Mockery;
use Pedrollo\Database\PostgresConnection;
use Pedrollo\Database\Schema\Blueprint;
use ReflectionException;
use ReflectionMethod;
use Tests\TestCase;

class BuilderTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testReturnsCorrectBlueprint()
    {
        $connection = Mockery::mock(PostgresConnection::class);
        $connection->makePartial();

        $builder = $connection->getSchemaBuilder();

        $method = new ReflectionMethod($builder,'createBlueprint');
        $method->setAccessible(true);
        $blueprint = $method->invoke($builder,'test',function () {});

        $this->assertInstanceOf(Blueprint::class, $blueprint);
    }
}
