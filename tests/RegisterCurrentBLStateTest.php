<?php
declare(strict_types=1);

namespace PluggitApi\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use PluggitApi\Tests\Helper\ModbusMasterMock;
use PluggitApi\Translation;

final class RegisterCurrentBLStateTest extends TestCase
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
     * @return array
     */
    public function provider(): array
    {
        return [
            [0, 'standby'],
            [1, 'manual'],
            [2, 'demand'],
            [3, 'week program'],
            [4, 'servo flow'],
            [5, 'away'],
            [6, 'summer'],
            [7, 'di override'],
            [8, 'hygrostat override'],
            [9, 'fireplace'],
            [10, 'installer'],
            [11, 'fail safe 1'],
            [12, 'fail safe 2'],
            [13, 'fail off'],
            [14, 'defrost off'],
            [15, 'defrost'],
            [16, 'night'],
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
        $register = new Helper\RegisterCurrentBLStateHelper($modbus, 40473, 'prmCurrentBLState', 'Current unit mode');
        $register->setValue($state);
        self::assertEquals($state, $register->getValue());
        self::assertEquals($expected, $register->getValue(true));
    }
}
