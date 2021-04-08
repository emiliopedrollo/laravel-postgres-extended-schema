<?php

namespace Tests\Query;

use DateTime;
use Mockery;
use Pedrollo\Database\PostgresConnection;
use Pedrollo\Database\Query\Builder;
use Tests\TestCase;

class BuilderTest extends TestCase
{
    public function testWithExpression()
    {
        $builder = $this->getConnection()->table('u')
            ->select('u.id')
            ->withExpression('u', $this->getConnection()->table('users'))
            ->withExpression('p', function (Builder $query) {
                $query->from('posts');
            })
            ->join('p', 'p.user_id', '=', 'u.id');

        $expected = 'with "u" as (select * from "users"), "p" as (select * from "posts") select "u"."id" from "u" inner join "p" on "p"."user_id" = "u"."id"';

        $this->assertEquals($expected, $builder->toSql());
    }

    public function testWithExpressionPostgres()
    {
        $builder = $this->getConnection()->query();
        $builder->select('u.id')
            ->from('u')
            ->withExpression('u', $this->getConnection()->query()->from('users'))
            ->withExpression('p', $this->getConnection()->query()->from('posts'))
            ->join('p', 'p.user_id', '=', 'u.id');

        $expected = 'with "u" as (select * from "users"), "p" as (select * from "posts") select "u"."id" from "u" inner join "p" on "p"."user_id" = "u"."id"';
        $this->assertEquals($expected, $builder->toSql());
    }

    public function testWithRecursiveExpression()
    {
        /** @noinspection SqlResolve */
        /** @noinspection SqlNoDataSourceInspection */
        $query = 'select 1 union all select number + 1 from numbers where number < 3';

        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $builder = $this->getConnection()->table('numbers')
            ->withRecursiveExpression('numbers', $query, ['number']);

        $expected = 'with recursive "numbers" ("number") as (select 1 union all select number + 1 from numbers where number < 3) select * from "numbers"';

        $this->assertEquals($expected, $builder->toSql());
    }

    public function testWithRecursiveExpressionPostgres()
    {
        $query = $this->getConnection()->query()
            ->selectRaw('1')
            ->unionAll(
                $this->getConnection()->query()
                    ->selectRaw('number + 1')
                    ->from('numbers')
                    ->where('number', '<', 3)
            );
        $builder = $this->getConnection()->query();
        $builder->from('numbers')
            ->withRecursiveExpression('numbers', $query, ['number']);

        $expected = 'with recursive "numbers" ("number") as ('.$query->toSql().') select * from "numbers"';
        $this->assertEquals($expected, $builder->toSql());
        $this->assertEquals([3], $builder->getRawBindings()['expressions']);
    }

    public function testInsertUsing()
    {

        $connection = $this->getConnection();

        $connection->shouldReceive('affectingStatement')
            ->andReturnUsing(function ($query, $bindings) {
                $expected = 'with "u" as (select "id" from "users" where "id" > ?) insert into "posts" ("user_id") select * from "u"';

                $this->assertEquals($expected,$query);
                $this->assertEquals([1],$bindings);

                return true;
            });

        $connection->shouldReceive('reconnectIfMissingConnection');

        $connection->pretend(function (PostgresConnection $connection){
            /** @var Builder $builder */
            $connection->query()->from('posts')
                ->withExpression('u', $connection
                    ->table('users')
                    ->select('id')
                    ->where('id', '>', 1)
                )->insertUsing(['user_id'],$connection->query()->from('u'));
        });

    }


    public function testUpdate()
    {
        $datetime = new DateTime();

        $connection = $this->getConnection();

        $connection->shouldReceive('update')
            ->andReturnUsing(function($query, $bindings) use ($datetime) {
                $expected = 'with "u" as (select * from "users" where "id" > ?) update "posts" set "user_id" = (select min(id) from u), "updated_at" = ?';
                $this->assertEquals($expected,$query);
                $this->assertEquals([1,$datetime],$bindings);
                return 0;
            });

        $connection->table('posts')
            ->withExpression('u', $connection->table('users')->where('id', '>', 1))
            ->update([
                'user_id' => $connection->raw('(select min(id) from u)'),
                'updated_at' => $datetime,
            ]);

    }

    public function testDelete()
    {
        $connection = $this->getConnection();

        $connection->shouldReceive('delete')
            ->andReturnUsing(function($query, $bindings) {
                $expected = 'with "u" as (select * from "users" where "id" > ?) delete from "posts" where "user_id" in (select "id" from "u")';
                $this->assertEquals($expected, $query);
                $this->assertEquals([1],$bindings);
                return 0;
            });

        $connection->table('posts')
            ->withExpression('u', $connection->table('users')->where('id', '>', 1))
            ->whereIn('user_id', $connection->table('u')->select('id'))
            ->delete();
    }


    /**
     * @return Mockery\Mock|PostgresConnection
     */
    protected function getConnection()
    {
        $connection = Mockery::mock(PostgresConnection::class);
        $connection->makePartial();
        $connection->useDefaultPostProcessor();
        $connection->useDefaultQueryGrammar();
        $connection->shouldAllowMockingProtectedMethods();
        return $connection;
    }
}
