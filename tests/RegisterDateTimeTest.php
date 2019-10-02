<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PluggitApi\Register\DateTime;

require_once __DIR__.DIRECTORY_SEPARATOR.'ModbusMasterMock.php';

final class RegisterDateTimeTest extends TestCase
{
    public function testGetValue(): void
    {
        $modbus = new ModbusMasterMock('127.0.0.1');
        $register = new DateTime($modbus, '108','40109', 'prmDateTime', 'Current date/time');

        $this->assertEquals(1570052849, $register->getValue(false));
        $this->assertEquals("2019-10-02T21:47:29+00:00", $register->getValue(true));

        $this->assertEquals(1570052849, $register->getValue(false, true));
        $this->assertEquals("2019-10-02T21:47:29+00:00", $register->getValue(true, true ));
    }
}
