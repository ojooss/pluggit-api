<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PluggitApi\Register\Numeric;
use PluggitApi\Translation;

require_once __DIR__.DIRECTORY_SEPARATOR.'ModbusMasterMock.php';

final class RegisterNumericTest extends TestCase
{

    public static function setUpBeforeClass(): void
    {
        // init with test language
        Translation::singleton('de');
    }


    public function testGetValue(): void
    {
        $modbus = new ModbusMasterMock('127.0.0.1');
        $register = new Numeric($modbus, '132', '40133', 'prmRamIdxT1', 'Outdoor temperature T1', '%s °C');
        $this->assertEquals(17.556928634643555, $register->getValue(false));
        $this->assertEquals("18 °C", $register->getValue(true));
    }
}
