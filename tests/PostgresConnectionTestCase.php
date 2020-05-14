<?php

namespace Tests;

use Mockery;
use Pedrollo\Database\PostgresConnection;
use Doctrine\DBAL\Driver\PDOPgSql\Driver;

class PostgresConnectionTestCase extends TestCase
{
    public function testReturnsDoctrineDriver()
    {
        $conn = Mockery::mock(PostgresConnection::class)->makePartial();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertInstanceOf(Driver::class, $conn->getDoctrineDriver());
    }
}
