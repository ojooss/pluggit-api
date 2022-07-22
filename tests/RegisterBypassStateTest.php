<?php
declare(strict_types=1);

namespace PluggitApi\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use PluggitApi\Tests\Helper\ModbusMasterMock;
use PluggitApi\Translation;

final class RegisterBypassStateTest extends TestCase
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
    public function provider(): array
    {
        return [
            [0, 'closed'],
            [1, 'in process'],
            [32, 'closing'],
            [64, 'opening'],
            [255, 'opened'],
            [999, 'unknown'],
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
        $register = new Helper\RegisterBypassStateHelper($modbus, 40199, 'prmRamIdxBypassActualState', 'Bypass state');
        $register->setValue($state);
        self::assertEquals($state, $register->getValue());
        self::assertEquals($expected, $register->getValue(true));
    }
}
