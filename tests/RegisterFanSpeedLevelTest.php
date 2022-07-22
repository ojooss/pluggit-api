<?php
declare(strict_types=1);

namespace PluggitApi\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use PluggitApi\Register\FanSpeedLevel;
use PluggitApi\Tests\Helper\ModbusMasterMock;
use PluggitApi\Translation;

final class RegisterFanSpeedLevelTest extends TestCase
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
        $register = new FanSpeedLevel($modbus, 40325, 'prmRomIdxSpeedLevel', 'Speed level of Fans', '%s');

        self::assertEquals(3, $register->getValue());
        self::assertEquals('3', $register->getValue(true));
    }

    /**
     * @throws Exception
     */
    public function testWriteValue()
    {
        $modbus = new ModbusMasterMock('127.0.0.1');
        $register = new FanSpeedLevel($modbus, 40325, 'prmRomIdxSpeedLevel', 'Speed level of Fans', '%s');

        $valueOk = 3;
        $register->writeValue($valueOk);
        self::assertEquals($valueOk, $register->getValue());

        try {
            $valueError = 5;
            $register->writeValue($valueError);
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            self::assertStringContainsString('invalid value for fan speed level', $e->getMessage());
        }
    }
}
