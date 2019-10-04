<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PluggitApi\Register\Floating;
use PluggitApi\Translation;

require_once __DIR__.DIRECTORY_SEPARATOR.'ModbusMasterMock.php';

final class RegisterFloatingTest extends TestCase
{

    public static function setUpBeforeClass(): void
    {
        // init with test language
        Translation::singleton('en');
    }


    public function testGetValue(): void
    {
        $modbus = new ModbusMasterMock('127.0.0.1');
        $register = new Floating($modbus, '132', '40133', 'prmRamIdxT1', 'Outdoor temperature T1', '%s °C', 1);
        $this->assertEquals(17.556928634643555, $register->getValue(false));
        $this->assertEquals("17.6 °C", $register->getValue(true));
    }
}
