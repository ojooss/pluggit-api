<?php
declare(strict_types=1);

namespace PluggitApi\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use PluggitApi\Register\FilterDefaultTime;
use PluggitApi\Tests\Helper\ModbusMasterMock;
use PluggitApi\Translation;

final class RegisterFilterDefaultTimeTest extends TestCase
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
        $register = new FilterDefaultTime($modbus, 40555, 'prmFilterDefaultTime', 'Filter Lifetime (Days)', '%s days');

        self::assertEquals(80, $register->getValue());
        self::assertEquals('80 days', $register->getValue(true));
    }

    /**
     * @throws Exception
     */
    public function testWriteValue()
    {
        $modbus = new ModbusMasterMock('127.0.0.1');
        $register = new FilterDefaultTime($modbus, 40555, 'prmFilterDefaultTime', 'Filter Lifetime (Days)', '%s days');

        $valueOk = 80;
        $register->writeValue($valueOk);
        self::assertEquals($valueOk, $register->getValue());

        try {
            $valueError = -1;
            $register->writeValue($valueError);
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            self::assertStringContainsString('invalid value for default filter time', $e->getMessage());
        }
    }
}
