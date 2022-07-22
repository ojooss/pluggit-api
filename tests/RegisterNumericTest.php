<?php
declare(strict_types=1);

namespace PluggitApi\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use PluggitApi\Register\Numeric;
use PluggitApi\Tests\Helper\ModbusMasterMock;
use PluggitApi\Translation;

final class RegisterNumericTest extends TestCase
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
        $register = new Numeric(
            $modbus,
            40555,
            'prmFilterRemainingTime',
            'Remaining time of the Filter Lifetime',
            '%s days'
        );
        self::assertEquals(80, $register->getValue());
        self::assertEquals("80 days", $register->getValue(true));
    }
}
