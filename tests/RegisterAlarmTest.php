<?php
declare(strict_types=1);

namespace PluggitApi\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use PluggitApi\Tests\Helper\ModbusMasterMock;
use PluggitApi\Tests\Helper\RegisterAlarmHelper;
use PluggitApi\Translation;

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
        $register = new RegisterAlarmHelper(
            $modbus,
            40517,
            'prmLastActiveAlarm',
            'If more Alarms are active, the alarm with highest number will be contained in the parameter.'
        );
        $register->setValue(3);
        self::assertEquals(3, $register->getValue());
        self::assertEquals('Bypass Alarm', $register->getValue(true));
    }
}
