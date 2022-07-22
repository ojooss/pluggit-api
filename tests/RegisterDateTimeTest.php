<?php
declare(strict_types=1);

namespace PluggitApi\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use PluggitApi\Register\DateTime;
use PluggitApi\Tests\Helper\ModbusMasterMock;
use PluggitApi\Translation;

final class RegisterDateTimeTest extends TestCase
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
        $register = new DateTime($modbus, 40109, 'prmDateTime', 'Current date/time', 'date-time-format');

        self::assertEquals(1570052849, $register->getValue());
        self::assertEquals("10/02/2019 21:47:29", $register->getValue(true));

        self::assertEquals(1570052849, $register->getValue(false, true));
        self::assertEquals("10/02/2019 21:47:29", $register->getValue(true, true));
    }
}
