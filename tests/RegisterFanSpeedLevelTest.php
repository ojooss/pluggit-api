<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PluggitApi\Register\FanSpeedLevel;
use PluggitApi\Translation;

require_once __DIR__.DIRECTORY_SEPARATOR.'ModbusMasterMock.php';


final class RegisterFanSpeedLevelTest extends TestCase
{

    public static function setUpBeforeClass(): void
    {
        // init with test language
        Translation::singleton('en');
    }

    public function testGetValue(): void
    {
        $modbus = new ModbusMasterMock('127.0.0.1');
        $register = new FanSpeedLevel($modbus, '324', '40325', 'prmRomIdxSpeedLevel', 'Speed level of Fans', '%s');

        $this->assertEquals(3, $register->getValue(false));
        $this->assertEquals('3', $register->getValue(true));
    }

    public function testWriteValue()
    {
        $modbus = new ModbusMasterMock('127.0.0.1');
        $register = new FanSpeedLevel($modbus, '324', '40325', 'prmRomIdxSpeedLevel', 'Speed level of Fans', '%s');

        $valueOk = 3;
        $register->writeValue($valueOk);
        $this->assertEquals($valueOk, $register->getValue());

        try {
            $valueError = 5;
            $register->writeValue($valueError);
            $this->fail('Exception not thrown');
        }
        catch (\Exception $e) {
            $this->assertStringContainsString('invalid value for fan speed level', $e->getMessage());
        }
    }

}
