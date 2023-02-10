<?php

namespace Tests\Schema;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use Pedrollo\Database\Schema\Blueprint;
use PHPUnit\Runner\TestHook;
use Tests\TestCase;

class BlueprintTest extends TestCase implements TestHook
{
    use MockeryPHPUnitIntegration;

    /** @var  Blueprint|MockInterface */
    protected $blueprint;

    public function setUp() : void
    {
        parent::setUp();

        $this->blueprint = Mockery::mock(Blueprint::class)
            ->makePartial()->shouldAllowMockingProtectedMethods();
    }


    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testInherits()
    {
        $this->blueprint
            ->shouldReceive('inehrits')
            ->with('anotherTable');

        $this->blueprint->inherits('anotherTable');
    }

    public function testPartitionBy()
    {
        $this->blueprint
            ->shouldReceive('partitionBy')
            ->with('range','timestamp');

        $this->blueprint->partitionBy('range', 'timestamp');
    }

    public function testGinIndex()
    {
        $this->blueprint
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('indexCommand')
            ->with('index', 'col', 'myName', 'gin');

        $this->blueprint->gin('col', 'myName');
    }

    public function testGistIndex()
    {
        $this->blueprint
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('indexCommand')
            ->with('index', 'col', 'myName', 'gist');

        $this->blueprint->gist('col', 'myName');
    }

    public function testCharacter()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('character', 'col', 14);

        $this->blueprint->character('col', 14);
    }

    public function testHstore()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('hstore', 'col');

        $this->blueprint->hstore('col');
    }

    public function testUuid()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('uuid', 'col');

        $this->blueprint->uuid('col');
    }

    public function testJsonb()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('jsonb', 'col');

        $this->blueprint->jsonb('col');
    }

    public function testInt4range()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('int4range', 'col');

        $this->blueprint->int4range('col');
    }

    public function testInt8range()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('int8range', 'col');

        $this->blueprint->int8range('col');
    }

    public function testNumRange()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('numrange', 'col');

        $this->blueprint->numrange('col');
    }

    public function testTSRange()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('tsrange', 'col');

        $this->blueprint->tsrange('col');
    }

    public function testTSTZRange()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('tstzrange', 'col');

        $this->blueprint->tstzrange('col');
    }


    public function testBit()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('bit', 'col');

        $this->blueprint->bit('col',2);
    }


    public function testBytea()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('bytea', 'col');

        $this->blueprint->tstzrange('col');
    }

    public function testCustomType()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('anewtype','col');

        $this->blueprint->custom('col','anewtype');
    }
}
