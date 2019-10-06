<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PluggitApi\Register\CurrentBLState;
use PluggitApi\Translation;

require_once __DIR__.DIRECTORY_SEPARATOR.'ModbusMasterMock.php';

class RegisterCurrentBLStateHelper extends CurrentBLState
{
    /**
     * @param $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

}

final class RegisterCurrentBLStateTest extends TestCase
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
    public function provider()
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
        $register = new RegisterCurrentBLStateHelper($modbus, '472', '40473', 'prmCurrentBLState', 'Current unit mode');
        $register->setValue($state);
        $this->assertEquals($state, $register->getValue(false));
        $this->assertEquals($expected, $register->getValue(true));
    }
}
