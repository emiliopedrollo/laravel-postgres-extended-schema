<?php

namespace Tests\Schema\Grammars;

use Mockery;
use Pedrollo\Database\Schema\Blueprint;
use Pedrollo\Database\PostgresConnection;
use Pedrollo\Database\Schema\Grammars\PostgresGrammar;
use Tests\TestCase;

class PostgresGrammarTest extends TestCase
{
    public function testAddingGinIndex()
    {
        $blueprint = new Blueprint('test');
        $blueprint->gin('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('CREATE INDEX', $statements[0]);
        $this->assertStringContainsString('GIN("foo")', $statements[0]);
    }

    public function testAddingGistIndex()
    {
        $blueprint = new Blueprint('test');
        $blueprint->gist('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('CREATE INDEX', $statements[0]);
        $this->assertStringContainsString('GIST("foo")', $statements[0]);
    }

    public function testAddingCharacter()
    {
        $blueprint = new Blueprint('test');
        $blueprint->character('foo', 14);
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('alter table', $statements[0]);
        $this->assertStringContainsString('add column "foo" character(14)', $statements[0]);
    }

    public function testAddingHstore()
    {
        $blueprint = new Blueprint('test');
        $blueprint->hstore('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('alter table', $statements[0]);
        $this->assertStringContainsString('add column "foo" hstore', $statements[0]);
    }

    public function testAddingUuid()
    {
        $blueprint = new Blueprint('test');
        $blueprint->uuid('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('alter table', $statements[0]);
        $this->assertStringContainsString('add column "foo" uuid', $statements[0]);
    }

    public function testAddingJsonb()
    {
        $blueprint = new Blueprint('test');
        $blueprint->jsonb('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('alter table', $statements[0]);
        $this->assertStringContainsString('add column "foo" jsonb', $statements[0]);
    }

    public function testAddingInt4range()
    {
        $blueprint = new Blueprint('test');
        $blueprint->int4range('foo');
        $statements = $blueprint->toSql(
            $this->getConnection(),
            $this->getGrammar()
        );

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('alter table', $statements[0]);
        $this->assertStringContainsString('add column "foo" int4range', $statements[0]);
    }

    public function testAddingInt8range()
    {
        $blueprint = new Blueprint('test');
        $blueprint->int8range('foo');
        $statements = $blueprint->toSql(
            $this->getConnection(),
            $this->getGrammar()
        );

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('alter table', $statements[0]);
        $this->assertStringContainsString('add column "foo" int8range', $statements[0]);
    }

    public function testAddingNumrange()
    {
        $blueprint = new Blueprint('test');
        $blueprint->numrange('foo');
        $statements = $blueprint->toSql(
            $this->getConnection(),
            $this->getGrammar()
        );

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('alter table', $statements[0]);
        $this->assertStringContainsString('add column "foo" numrange', $statements[0]);
    }

    public function testAddingTSRange()
    {
        $blueprint = new Blueprint('test');
        $blueprint->tsrange('foo');
        $statements = $blueprint->toSql(
            $this->getConnection(),
            $this->getGrammar()
        );

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('alter table', $statements[0]);
        $this->assertStringContainsString('add column "foo" tsrange', $statements[0]);
    }

    public function testAddingTSTZRange()
    {
        $blueprint = new Blueprint('test');
        $blueprint->tstzrange('foo');
        $statements = $blueprint->toSql(
            $this->getConnection(),
            $this->getGrammar()
        );

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('alter table', $statements[0]);
        $this->assertStringContainsString('add column "foo" tstzrange', $statements[0]);
    }

    public function testAddingDataRange()
    {
        $blueprint = new Blueprint('test');
        $blueprint->daterange('foo');
        $statements = $blueprint->toSql(
            $this->getConnection(),
            $this->getGrammar()
        );

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('alter table', $statements[0]);
        $this->assertStringContainsString('add column "foo" daterange', $statements[0]);
    }

    public function testAddingTSVector()
    {
        $blueprint = new Blueprint('test');
        $blueprint->tsvector('foo');
        $statements = $blueprint->toSql(
            $this->getConnection(),
            $this->getGrammar()
        );

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('alter table', $statements[0]);
        $this->assertStringContainsString('add column "foo" tsvector', $statements[0]);
    }

    public function testAddingCustomType()
    {
        $blueprint = new Blueprint('test');
        $blueprint->custom('foo','bar');
        $statements = $blueprint->toSql(
            $this->getConnection(),
            $this->getGrammar()
        );

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('alter table', $statements[0]);
        $this->assertStringContainsString('add column "foo" bar', $statements[0]);
    }

    /**
     * @return PostgresConnection
     */
    protected function getConnection()
    {
        return Mockery::mock(PostgresConnection::class);
    }

    protected function getGrammar()
    {
        return new PostgresGrammar();
    }
}
