<?php

namespace Tests\Schema\Grammars;

use Illuminate\Database\Query\Expression;
use Mockery;
use Pedrollo\Database\Schema\Blueprint;
use Pedrollo\Database\PostgresConnection;
use Pedrollo\Database\Schema\Grammars\PostgresGrammar;
use Tests\TestCase;

class PostgresGrammarTest extends TestCase
{
    public function testCreateWithInherits()
    {
        $blueprint = new Blueprint('test');
        $blueprint->create();
        $blueprint->uuid('id');
        $blueprint->inherits('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('create table', $statements[0]);
        $this->assertStringContainsString('inherits ("foo")', $statements[0]);
    }

    public function testCreateWithPartitionBy()
    {
        $blueprint = new Blueprint('test');
        $blueprint->create();
        $blueprint->timestamp('foo');
        $blueprint->partitionBy('range','foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('create table', $statements[0]);
        $this->assertStringContainsString('partition by range ("foo")', $statements[0]);
    }

    public function testCreateWithStorageParameters()
    {
        $blueprint = new Blueprint('test');
        $blueprint->create();
        $blueprint->timestamp('foo');
        $blueprint->with('bar','fur');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(2, $statements);
        $this->assertStringContainsString('create table', $statements[0]);
        $this->assertStringContainsString('set (bar = "fur")', $statements[1]);
    }

    public function testCreateWithMultipleStorageParameters()
    {
        $blueprint = new Blueprint('test');
        $blueprint->create();
        $blueprint->timestamp('foo');
        $blueprint->with('bar','fur');
        $blueprint->with('baz',0.42);
        $blueprint->with('buz',false);
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(4, $statements);
        $this->assertStringContainsString('create table', $statements[0]);
        $this->assertStringContainsString('set (bar = "fur")', $statements[1]);
        $this->assertStringContainsString('set (baz = 0.42)', $statements[2]);
        $this->assertStringContainsString('set (buz = false)', $statements[3]);
    }

    public function testCreateWithPartitionByExpression()
    {
        $blueprint = new Blueprint('test');
        $blueprint->create();
        $blueprint->timestamp('foo');
        $blueprint->partitionBy('range',new Expression('foo'));
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('create table', $statements[0]);
        $this->assertStringContainsString('partition by range (foo)', $statements[0]);
    }

    public function testCreateWithPartitionByMultipleValues()
    {
        $blueprint = new Blueprint('test');
        $blueprint->create();
        $blueprint->timestamp('foo');
        $blueprint->uuid('bar');
        $blueprint->partitionBy('hash',[new Expression('foo'),"bar"]);
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('create table', $statements[0]);
        $this->assertStringContainsString('partition by hash (foo, "bar")', $statements[0]);
    }

    public function testAddingIndex()
    {
        $blueprint = new Blueprint('test');
        $blueprint->index('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('create index', $statements[0]);
    }

    public function testDroppingIndex()
    {
        $blueprint = new Blueprint('test');
        $blueprint->dropIndex('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('drop index', $statements[0]);
    }

    public function testForeignKey()
    {
        $blueprint = new Blueprint('test');
        $blueprint->foreign('foo')->references('id')->on('bar');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('alter table', $statements[0]);
    }

    public function testAddingUniqueIndex()
    {
        $blueprint = new Blueprint('test');
        $blueprint->unique('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('create unique index', $statements[0]);
    }

    public function testDroppingUniqueIndex()
    {
        $blueprint = new Blueprint('test');
        $blueprint->dropUnique('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('drop index', $statements[0]);
    }

    public function testAddingNullsDistinctIndex()
    {
        $blueprint = new Blueprint('test');
        $blueprint->index('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('create index', $statements[0]);
        $this->assertStringNotContainsString('nulls distinct', $statements[0]);
        $this->assertStringNotContainsString('nulls not distinct', $statements[0]);
    }

    public function testAddingNullsNotDistinctIndex()
    {
        $blueprint = new Blueprint('test');
        $blueprint->index('foo')->dontDistinctNulls();
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('create index', $statements[0]);
        $this->assertStringContainsString('nulls not distinct', $statements[0]);
    }

    public function testAddingConcurrentIndex()
    {
        $blueprint = new Blueprint('test');
        $blueprint->index('foo')->concurrently();
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('create index concurrently', $statements[0]);
    }

    public function testAddingIndexWithWhere()
    {
        $blueprint = new Blueprint('test');
        $blueprint->index('foo')->where('bar is null');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('where bar is null', $statements[0]);
    }

    public function testAddingGinIndex()
    {
        $blueprint = new Blueprint('test');
        $blueprint->gin('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('create index', $statements[0]);
        $this->assertStringContainsString('using gin ("foo")', $statements[0]);
    }

    public function testAddingGistIndex()
    {
        $blueprint = new Blueprint('test');
        $blueprint->gist('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('create index', $statements[0]);
        $this->assertStringContainsString('using gist ("foo")', $statements[0]);
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

    public function testAddingBit()
    {
        $blueprint = new Blueprint('test');
        $blueprint->bit('foo',8);
        $statements = $blueprint->toSql(
            $this->getConnection(),
            $this->getGrammar()
        );

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('alter table', $statements[0]);
        $this->assertStringContainsString('add column "foo" bit(8)', $statements[0]);
    }

    public function testAddingBytea()
    {
        $blueprint = new Blueprint('test');
        $blueprint->bytea('foo');
        $statements = $blueprint->toSql(
            $this->getConnection(),
            $this->getGrammar()
        );

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('alter table', $statements[0]);
        $this->assertStringContainsString('add column "foo" bytea', $statements[0]);
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
