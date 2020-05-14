<?php

namespace Tests\Connectors;

use Illuminate\Container\Container;
use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\ConnectionFactory;
use Mockery;
use PDO;
use Pedrollo\Database\PostgresConnection;
use Tests\BaseTestCase;

class ConnectionFactoryBaseTest extends BaseTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        if (!Connection::getResolver('pgsql')) {
            Connection::resolverFor('pgsql', function ($connection, $database, $prefix, $config) {
                return new PostgresConnection($connection, $database, $prefix, $config);
            });
        }
    }

    public function testMakeCallsCreateConnection()
    {
        $pgConfig = [ 'driver' => 'pgsql', 'prefix' => 'prefix', 'database' => 'database', 'name' => 'foo' ];
        $pdo      = new DatabaseConnectionFactoryPDOStub;


        $factory = Mockery::mock(ConnectionFactory::class, [ new Container() ])->makePartial();
        $factory->shouldAllowMockingProtectedMethods();
        /** @noinspection PhpUndefinedMethodInspection */
        $conn    = $factory->createConnection('pgsql', $pdo, 'database', 'prefix', $pgConfig);

        $this->assertInstanceOf(PostgresConnection::class, $conn);
    }
}

class DatabaseConnectionFactoryPDOStub extends PDO
{
    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct()
    {
    }
}
