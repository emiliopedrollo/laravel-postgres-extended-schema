<?php

use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    public function tearDown() : void
    {
        Mockery::close();
    }
}
