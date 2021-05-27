<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PluggitApi\Register;
require_once __DIR__.DIRECTORY_SEPARATOR.'ModbusMasterMock.php';

class RegisterHelper extends Register
{
    /**
     * @return int
     */
    protected function readValue(): int
    {
        return 42;
    }

    /**
     * @param $value
     * @return string
     */
    protected function formatValue($value): string
    {
        return "0-8-15";
    }

}

final class RegisterTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testGetValue(): void
    {
        $modbusMaster = new ModbusMasterMock('127.0.0.1');
        $register = new RegisterHelper($modbusMaster, '9999', '123', 'Test', 'Test item');

        $this->assertEquals(42, $register->getValue(false));
        $this->assertEquals("0-8-15", $register->getValue(true));
    }

    /**
     * @throws Exception
     */
    public function testWriteAble():void
    {
        $modbusMaster = new ModbusMasterMock('127.0.0.1');
        $register = new RegisterHelper($modbusMaster, '9999', '123', 'Test', 'Test item');

        $this->assertFalse($register->isWriteAble());
    }
}

