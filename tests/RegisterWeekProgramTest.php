<?php
declare(strict_types=1);

namespace PluggitApi\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use PluggitApi\Register\WeekProgram;
use PluggitApi\Tests\Helper\ModbusMasterMock;
use PluggitApi\Translation;

final class RegisterWeekProgramTest extends TestCase
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
        $register = new WeekProgram($modbus, 40325, 'prmRomIdxSpeedLevel', 'Speed level of Fans', '%s');

        self::assertEquals(3, $register->getValue());
        self::assertEquals('3', $register->getValue(true));
    }

    /**
     * @throws Exception
     */
    public function testWriteValue()
    {
        $modbus = new ModbusMasterMock('127.0.0.1');
        $register = new WeekProgram(
            $modbus,
            40467,
            'prmNumOfWeekProgram',
            'Number of the Active Week Program (for Week Program mode)'
        );

        $valueOk = 10;
        $register->writeValue($valueOk);
        self::assertEquals($valueOk, $register->getValue());

        try {
            $valueError = 15;
            $register->writeValue($valueError);
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            self::assertStringContainsString('invalid value for week program', $e->getMessage());
        }
    }
}
