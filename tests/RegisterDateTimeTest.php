<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PluggitApi\Register\DateTime;
use PluggitApi\Translation;

require_once __DIR__.DIRECTORY_SEPARATOR.'ModbusMasterMock.php';

final class RegisterDateTimeTest extends TestCase
{

    public static function setUpBeforeClass(): void
    {
        // init with test language
        Translation::singleton('de');
    }

    public function testGetValue(): void
    {
        $modbus = new ModbusMasterMock('127.0.0.1');
        $register = new DateTime($modbus, '108','40109', 'prmDateTime', 'Current date/time', 'date-time-format');

        $this->assertEquals(1570052849, $register->getValue(false));
        $this->assertEquals("02.10.2019 21:47:29", $register->getValue(true));

        $this->assertEquals(1570052849, $register->getValue(false, true));
        $this->assertEquals("02.10.2019 21:47:29", $register->getValue(true, true ));
    }
}
