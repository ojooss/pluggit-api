<?php
declare(strict_types=1);

namespace PluggitApi\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use PluggitApi\Register\UnitMode;
use PluggitApi\Tests\Helper\ModbusMasterMock;
use PluggitApi\Translation;

final class RegisterUnitModeTest extends TestCase
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
     * @return array
     */
    public static function provider(): array
    {
        return [
            [2, 'demand'],
            [4, 'manual'],
            [8, 'program'],
            [16, 'away'],
            [32784, 'away-end'],
            [64, 'fireplace'],
            [32832, 'fireplace-end'],
            [2048, 'summer'],
            [34816, 'summer-end'],
            [128, 'bypass-enable'],
            [32896, 'bypass-disable'],
            [99, 'unknown'],
        ];
    }

    /**
     * @dataProvider provider
     * @param $state
     * @param $expected
     * @throws Exception
     */
    public function testGetValue($state, $expected): void
    {
        $modbus = new ModbusMasterMock('127.0.0.1');
        $register = new Helper\RegisterUnitModeHelper($modbus, 40473, 'prmCurrentBLState', 'Current unit mode');
        $register->setValue($state);
        self::assertEquals($state, $register->getValue());
        self::assertEquals($expected, $register->getValue(true));
    }

    /**
     * @throws Exception
     */
    public function testWriteValue()
    {
        $modbus = new ModbusMasterMock('127.0.0.1');
        $register = new UnitMode($modbus, 40169, 'prmRamIdxUnitMode', 'Active Unit mode');

        $valueOk = 8;
        $register->writeValue($valueOk);
        self::assertEquals($valueOk, $register->getValue());

        try {
            $valueError = 42;
            $register->writeValue($valueError);
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            self::assertStringContainsString('invalid value for unit mode', $e->getMessage());
        }
    }
}
