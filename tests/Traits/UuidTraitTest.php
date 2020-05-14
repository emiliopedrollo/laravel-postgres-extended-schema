<?php

namespace Tests\Traits;

use Exception;
use Pedrollo\Database\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;
use Tests\TestCase;

class UuidTraitTest extends TestCase
{
    public function testTraitIsBooted()
    {
        $model = new UuidBootingModelStub();
        $this->assertTrue($model::$uuidBooted);
    }

    /**
     * @throws Exception
     */
    public function testRequiredIncrementingFalse()
    {
        $this->expectException(RuntimeException::class);
        $model = new UuidAssignsUuidStub();
        $model->_provideUuidKey();
    }

    /**
     * @throws Exception
     */
    public function testUuidAssignsUuid()
    {
        $model = new UuidAssignsUuidStub2();

        $model->_provideUuidKey();

        $this->assertEquals(4, substr_count($model->getKey(), '-'));
    }
}

class UuidBootingModelStub extends Model
{
    use UuidTrait;

    public static $uuidBooted = false;

    public static function bootUuidTrait()
    {
        static::$uuidBooted = true;
    }
}

class UuidAssignsUuidStub extends Model
{
    use UuidTrait;

    public $timestamps = false;
    public $incrementing = true;

    /**
     * @throws Exception
     */
    public function _provideUuidKey()
    {
        $this->provideUuidKey($this);
    }
}

class UuidAssignsUuidStub2 extends UuidAssignsUuidStub
{
    public $incrementing = false;
}
