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

    public function testJoinLateral()
    {
        $builder = $this->getConnection()->query();
        $builder->select(['*'])->from('users')
            ->joinLateral('data','users_id','=','users.id');

        $expected = 'select * from "users" inner join lateral data on "users_id" = "users"."id"';
        $this->assertEquals($expected, $builder->toSql());
    }

    public function testJoinLateralSub()
    {
        $builder = $this->getConnection()->query();
        $builder->select(['*'])->from('users')
            ->joinLateralSub( $this->getConnection()->query()
                ->select(['*'])
                ->from('data'),
                'data', 'users_id','=','users.id');

        $expected = 'select * from "users" inner join lateral (select * from "data") as "data" on "users_id" = "users"."id"';
        $this->assertEquals($expected, $builder->toSql());
    }

    public function testJoinCrossLateralSub()
    {
        $builder = $this->getConnection()->query();
        $builder->select(['*'])->from('users')
            ->crossJoinLateralSub( $this->getConnection()->query()
                ->select(['*'])
                ->from('data'),
                'data');

        $expected = 'select * from "users" cross join lateral (select * from "data") as "data"';
        $this->assertEquals($expected, $builder->toSql());
    }

    public function testJoinCrossLateral()
    {
        $builder = $this->getConnection()->query();
        $builder->select(['*'])->from('users')
            ->crossJoinLateral('data');

        $expected = 'select * from "users" cross join lateral data';
        $this->assertEquals($expected, $builder->toSql());
    }

    public function testJoinLeftLateral()
    {
        $builder = $this->getConnection()->query();
        $builder->select(['*'])->from('users')
            ->leftLateralJoin('data', 'users_id','=','users.id');

        $expected = 'select * from "users" left join lateral data on "users_id" = "users"."id"';
        $this->assertEquals($expected, $builder->toSql());
    }

    public function testJoinRightLateral()
    {
        $builder = $this->getConnection()->query();
        $builder->select(['*'])->from('users')
            ->rightLateralJoin('data', 'users_id','=','users.id');

        $expected = 'select * from "users" right join lateral data on "users_id" = "users"."id"';
        $this->assertEquals($expected, $builder->toSql());
    }

    public function testLateralLeftJoinSub()
    {
        $builder = $this->getConnection()->query();
        $builder->select(['*'])->from('users')
            ->leftLateralJoinSub(
                $this->getConnection()->query()->select('*')->from('roles'),
                'roles','user_id','=','users.id'
            );

        $expected = 'select * from "users" left join lateral (select * from "roles") as "roles" on "user_id" = "users"."id"';
        $this->assertEquals($expected, $builder->toSql());
    }

    public function testLateralRightJoinSub()
    {
        $builder = $this->getConnection()->query();
        $builder->select(['*'])->from('users')
            ->rightLateralJoinSub(
                $this->getConnection()->query()->select('*')->from('roles'),
                'roles','user_id','=','users.id'
            );

        $expected = 'select * from "users" right join lateral (select * from "roles") as "roles" on "user_id" = "users"."id"';
        $this->assertEquals($expected, $builder->toSql());
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
