<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PluggitApi\Register;
require_once __DIR__.DIRECTORY_SEPARATOR.'ModbusMasterMock.php';

class RegisterHelper extends Register
{
    /**
     * @return mixed
     */
    protected function readValue()
    {
        return 42;
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function formatValue($value)
    {
        return "0-8-15";
    }

}

final class RegisterTest extends TestCase
{
    public function testGetValue(): void
    {
        $modbusMaster = new ModbusMasterMock('127.0.0.1');
        $register = new RegisterHelper($modbusMaster, '9999', '123', 'Test', 'Test item');

        $this->assertEquals(42, $register->getValue(false));
        $this->assertEquals("0-8-15", $register->getValue(true));
    }

    public function testWriteAble():void
    {
        $modbusMaster = new ModbusMasterMock('127.0.0.1');
        $register = new RegisterHelper($modbusMaster, '9999', '123', 'Test', 'Test item');

        $this->assertFalse($register->isWriteAble());
    }
}

