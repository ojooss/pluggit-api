<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PluggitApi\Register\Numeric;
use PluggitApi\Translation;

require_once __DIR__.DIRECTORY_SEPARATOR.'ModbusMasterMock.php';

final class RegisterNumericTest extends TestCase
{

    /**
     * @throws Exception
     */
    public static function setUpBeforeClass(): void
    {
        // init with test language
        Translation::singleton('en');
    }


    /**
     * @throws Exception
     */
    public function testGetValue(): void
    {
        $modbus = new ModbusMasterMock('127.0.0.1');
        $register = new Numeric($modbus, '554', '40555', 'prmFilterRemainingTime', 'Remaining time of the Filter Lifetime', '%s days');
        $this->assertEquals(80, $register->getValue(false));
        $this->assertEquals("80 days", $register->getValue(true));
    }
}
