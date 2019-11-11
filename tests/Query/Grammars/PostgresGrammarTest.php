<?php

use Pedrollo\Database\Query\Grammars\PostgresGrammar;
use Illuminate\Database\Query\Builder;

class PostgresGrammarTest extends BaseTestCase
{
    public function testHstoreWrapValue()
    {
        /** @var PostgresGrammar $grammar */
        $grammar = Mockery::mock(PostgresGrammar::class)->makePartial();

        $this->assertEquals('a => b', $grammar->wrap('[a => b]'));
    }

    public function testJsonWrapValue()
    {
        /** @var PostgresGrammar $grammar */
        $grammar = Mockery::mock(PostgresGrammar::class)->makePartial();

        $this->assertEquals('"a"->\'b\'', $grammar->wrap("a->'b'"));
        $this->assertEquals('"a"->>\'b\'', $grammar->wrap("a->>'b'"));
        $this->assertEquals('"a"#>\'b\'', $grammar->wrap("a#>'b'"));
        $this->assertEquals('"a"#>>\'b\'', $grammar->wrap("a#>>'b'"));
    }

    public function testWhereNotNull()
    {
        /** @var PostgresGrammar $grammar */
        $grammar = Mockery::mock(PostgresGrammar::class)->makePartial();
        $builder = Mockery::mock(Builder::class);
        $where = [
            'column' => "a->>'b'"
        ];

        $this->assertEquals('("a"->>\'b\') is not null', $grammar->whereNotNull($builder, $where));
    }

    public function testWhereNull()
    {
        /** @var PostgresGrammar|\Mockery\MockInterface $grammar */
        $grammar = Mockery::mock(PostgresGrammar::class)->makePartial();
        $builder = Mockery::mock(Builder::class);
        $where = [
            'column' => "a->>'b'"
        ];

        $this->assertEquals('("a"->>\'b\') is null', $grammar->whereNull($builder, $where));
    }
}
