<?php
declare(strict_types=1);

namespace PluggitApi\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use PluggitApi\Register\Floating;
use PluggitApi\Tests\Helper\ModbusMasterMock;
use PluggitApi\Translation;

final class RegisterFloatingTest extends TestCase
{

    /**
     * @throws Exception
     */
    public static function setUpBeforeClass(): void
    {
        // init with test language
        Translation::singleton();
    }


    /**
     * @throws Exception
     */
    public function testGetValue(): void
    {
        $modbus = new ModbusMasterMock('127.0.0.1');
        $register = new Floating($modbus, 40133, 'prmRamIdxT1', 'Outdoor temperature T1', '%s °C', 1);
        self::assertEquals(17.556928634643555, $register->getValue());
        self::assertEquals("17.6 °C", $register->getValue(true));
    }
}
