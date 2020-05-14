<?php

namespace Tests\Schema;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use Pedrollo\Database\Schema\Blueprint;
use PHPUnit\Runner\TestHook;
use Tests\BaseTestCase;

class BlueprintBaseTest extends BaseTestCase implements TestHook
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

    public function testGinIndex()
    {
        /** @noinspection PhpParamsInspection */
        $this->blueprint
            ->shouldReceive('indexCommand')
            ->with('gin', 'col', 'myName');

        $this->blueprint->gin('col', 'myName');
    }

    public function testGistIndex()
    {
        /** @noinspection PhpParamsInspection */
        $this->blueprint
            ->shouldReceive('indexCommand')
            ->with('gist', 'col', 'myName');

        $this->blueprint->gist('col', 'myName');
    }

    public function testCharacter()
    {
        /** @noinspection PhpParamsInspection */
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('character', 'col', 14);

        $this->blueprint->character('col', 14);
    }

    public function testHstore()
    {
        /** @noinspection PhpParamsInspection */
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('hstore', 'col');

        $this->blueprint->hstore('col');
    }

    public function testUuid()
    {
        /** @noinspection PhpParamsInspection */
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('uuid', 'col');

        $this->blueprint->uuid('col');
    }

    public function testJsonb()
    {
        /** @noinspection PhpParamsInspection */
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('jsonb', 'col');

        $this->blueprint->jsonb('col');
    }

    public function testInt4range()
    {
        /** @noinspection PhpParamsInspection */
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('int4range', 'col');

        $this->blueprint->int4range('col');
    }

    public function testInt8range()
    {
        /** @noinspection PhpParamsInspection */
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('int8range', 'col');

        $this->blueprint->int8range('col');
    }

    public function testNumRange()
    {
        /** @noinspection PhpParamsInspection */
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('numrange', 'col');

        $this->blueprint->numrange('col');
    }

    public function testTSRange()
    {
        /** @noinspection PhpParamsInspection */
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('tsrange', 'col');

        $this->blueprint->tsrange('col');
    }

    public function testTSTZRange()
    {
        /** @noinspection PhpParamsInspection */
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('tstzrange', 'col');

        $this->blueprint->tstzrange('col');
    }
}
