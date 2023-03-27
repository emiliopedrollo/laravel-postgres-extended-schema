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
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }


    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testInherits()
    {
        $this->blueprint
            ->shouldReceive('inherits')
            ->withArgs(['anotherTable'])
            ->once();

        $this->blueprint->inherits('anotherTable');
    }

    public function testPartitionBy()
    {
        $this->blueprint
            ->shouldReceive('partitionBy')
            ->withArgs(['range','timestamp'])
            ->once();

        $this->blueprint->partitionBy('range', 'timestamp');
    }

    public function testGinIndex()
    {
        $this->blueprint
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('indexCommand')
            ->withArgs(['index', 'col', 'myName', 'gin'])
            ->once();

        $this->blueprint->gin('col', 'myName');
    }

    public function testGistIndex()
    {
        $this->blueprint
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('indexCommand')
            ->withArgs(['index', 'col', 'myName', 'gist'])
            ->once();

        $this->blueprint->gist('col', 'myName');
    }

    public function testCharacter()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->withArgs(['character', 'col', ['length' => 14]])
            ->once();

        $this->blueprint->character('col', 14);
    }

    public function testHstore()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->withArgs(['hstore', 'col'])
            ->once();

        $this->blueprint->hstore('col');
    }

    public function testUuid()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->withArgs(['uuid', 'col'])
            ->once();

        $this->blueprint->uuid('col');
    }

    public function testJsonb()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->withArgs(['jsonb', 'col'])
            ->once();

        $this->blueprint->jsonb('col');
    }

    public function testInt4range()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->withArgs(['int4range', 'col'])
            ->once();

        $this->blueprint->int4range('col');
    }

    public function testInt8range()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->withArgs(['int8range', 'col'])
            ->once();

        $this->blueprint->int8range('col');
    }

    public function testNumRange()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->withArgs(['numrange', 'col'])
            ->once();

        $this->blueprint->numrange('col');
    }

    public function testTSRange()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->withArgs(['tsrange', 'col'])
            ->once();

        $this->blueprint->tsrange('col');
    }

    public function testTSTZRange()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->withArgs(['tstzrange', 'col'])
            ->once();

        $this->blueprint->tstzrange('col');
    }


    public function testBit()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->withArgs(['bit', 'col', ['length' => 2]])
            ->once();

        $this->blueprint->bit('col',2);
    }


    public function testBytea()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->withArgs(['bytea', 'col'])
            ->once();

        $this->blueprint->bytea('col');
    }

    public function testCustomType()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->withArgs(['anewtype','col'])
            ->once();

        $this->blueprint->custom('col','anewtype');
    }
}
