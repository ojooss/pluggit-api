<?php
declare(strict_types=1);

namespace PluggitApi\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use PluggitApi\Tests\Helper\ModbusMasterMock;
use PluggitApi\Tests\Helper\RegisterHelper;

final class RegisterTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testGetValue(): void
    {
        $modbusMaster = new ModbusMasterMock('127.0.0.1');
        $register = new RegisterHelper($modbusMaster, 9999, '123', 'Test', 'Test item');

        self::assertEquals(42, $register->getValue());
        self::assertEquals("0-8-15", $register->getValue(true));
    }

    /**
     * @throws Exception
     */
    public function testWriteAble():void
    {
        $modbusMaster = new ModbusMasterMock('127.0.0.1');
        $register = new RegisterHelper($modbusMaster, 9999, '123', 'Test', 'Test item');

        self::assertFalse($register->isWriteAble());
    }
}
