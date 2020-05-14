<?php

namespace Tests\Eloquent;

use Illuminate\Database\Query\Builder;
use Mockery;
use Mockery\Mock;
use Pedrollo\Database\PostgresConnection;
use PHPUnit\Framework\TestCase;
use Tests\Models\User;

class ModelTest extends TestCase
{
    public function testWithExpression()
    {
        $user = Mockery::mock(User::class);
        /** @noinspection PhpParamsInspection */
        $user->shouldReceive('getConnection')->andReturn($this->getConnection());
        $user->makePartial();

        /** @noinspection PhpUndefinedMethodInspection */
        $sql = $user->withExpression('ids', 'select 1 union all select 2', ['id'])
            ->whereIn('id', function (Builder $query) {
                $query->from('ids');
            })->toSql();

        $expected = 'with "ids" ("id") as (select 1 union all select 2) select * from "users" where "id" in (select * from "ids")';

        $this->assertEquals($expected,$sql);

        Mockery::close();
    }

    public function testWithRecursiveExpression()
    {
        $user = Mockery::mock(User::class);
        /** @noinspection PhpParamsInspection */
        $user->shouldReceive('getConnection')->andReturn($this->getConnection());
        $user->makePartial();

        /** @noinspection PhpUndefinedMethodInspection */
        $query = $user
            ->where('id', 3)
            ->unionAll(
                $user->select('users.*')
                    ->join('parents', 'parents.parent_id', '=', 'users.id')
            );

        $sql = $query->toSql();

        $users_sql = $query->from('parents')
            ->withRecursiveExpression('parents', $query)
            ->toSql();

        $expected_sql = '(select * from "users" where "id" = ?) union all (select "users".* from "users" inner join "parents" on "parents"."parent_id" = "users"."id")';

        $expected_users_sql = '(with recursive "parents" as ((select * from "parents" where "id" = ?) union all (select "users".* from "users" inner join "parents" on "parents"."parent_id" = "users"."id")) select * from "parents" where "id" = ?) union all (select "users".* from "users" inner join "parents" on "parents"."parent_id" = "users"."id")';

        $this->assertEquals($expected_sql,$sql);
        $this->assertEquals($expected_users_sql,$users_sql);

        Mockery::close();
    }

    /**
     * @return Mock|PostgresConnection
     */
    protected function getConnection()
    {
        $connection = Mockery::mock(PostgresConnection::class);
        $connection->makePartial();
        $connection->useDefaultPostProcessor();
        $connection->useDefaultQueryGrammar();
        return $connection;
    }
}
