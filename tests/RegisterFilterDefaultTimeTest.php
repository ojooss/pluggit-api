<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PluggitApi\Register\FilterDefaultTime;
use PluggitApi\Translation;

require_once __DIR__.DIRECTORY_SEPARATOR.'ModbusMasterMock.php';

final class RegisterFilterDefaultTimeTest extends TestCase
{

    public static function setUpBeforeClass(): void
    {
        // init with test language
        Translation::singleton('en');
    }

    public function testGetValue(): void
    {
        $modbus = new ModbusMasterMock('127.0.0.1');
        $register = new FilterDefaultTime($modbus, '556', '40555', 'prmFilterDefaultTime', 'Filter Lifetime (Days)', '%s days');

        $this->assertEquals(90, $register->getValue(false));
        $this->assertEquals('90 days', $register->getValue(true));
    }

    public function testWriteValue()
    {
        $modbus = new ModbusMasterMock('127.0.0.1');
        $register = new FilterDefaultTime($modbus, '556', '40555', 'prmFilterDefaultTime', 'Filter Lifetime (Days)', '%s days');

        $valueOk = 90;
        $register->writeValue($valueOk);
        $this->assertEquals($valueOk, $register->getValue());

        try {
            $valueError = 100;
            $register->setValue($valueError);
            $register->writeValue($valueError);
            $this->fail('Exception not thrown');
        }
        catch (\Exception $e) {
            $this->assertStringContainsString('invalid value for default filter time', $e->getMessage());
        }
    }

}
