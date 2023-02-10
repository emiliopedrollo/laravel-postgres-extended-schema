<?php

namespace Tests;

use Mockery;
use Pedrollo\Database\PostgresConnection;
use Doctrine\DBAL\Driver;

class PostgresConnectionTestCase extends TestCase
{
    public function testReturnsDoctrineDriver()
    {
        $conn = Mockery::mock(PostgresConnection::class)->makePartial();
        $this->assertInstanceOf(Driver::class, $conn->getDoctrineConnection()->getDriver());
    }
}
