<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PluggitApi\Register\Alarm;
use PluggitApi\Translation;

require_once __DIR__.DIRECTORY_SEPARATOR.'ModbusMasterMock.php';

class RegisterAlarmHelper extends Alarm
{
    /**
     * @param $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

}

final class RegisterAlarmTest extends TestCase
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
        $register = new RegisterAlarmHelper($modbus, 40517, 'prmLastActiveAlarm', 'If more Alarms are active, the alarm with highest number will be contained in the parameter.');
        $register->setValue(3);
        self::assertEquals(3, $register->getValue(false));
        self::assertEquals('Bypass Alarm', $register->getValue(true));
    }

}
