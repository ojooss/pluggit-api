<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PluggitApi\VentilationUnit;

require_once __DIR__.DIRECTORY_SEPARATOR.'ModbusMasterMock.php';

class VentilationUnitHelper extends VentilationUnit
{
    public function __construct($ipAddress)
    {
        parent::__construct($ipAddress);

        $modbus = new ModbusMasterMock($ipAddress);

        foreach ($this->register as $item) {
            $item->setModbus($modbus);
        }
    }
}

final class VentilationUnitTest extends TestCase
{
    /**
     * @return array
     */
    public function provider()
    {
        return [
            ['getCurrentDateTime', 1570052849, '2019-10-02T21:47:29+00:00'],
            ['getStartExploitationDate', 1506794566, '2017-09-30T18:02:46+00:00'],
            ['getOutdoorTemperature', 17.556928634643555, '18 °C'],
        ];
    }

    /**
     * @dataProvider provider
     */
    public function testRegisterGetter($getterName, $expectedRaw, $expectedFormated):void
    {
        $ventilationUnit = new VentilationUnitHelper('127.0.0.1');
        $this->assertEquals($expectedRaw, $ventilationUnit->$getterName(), 'Error in '.$getterName);
        $this->assertEquals($expectedFormated, $ventilationUnit->$getterName(true), 'Error in '.$getterName);
    }

    /**
     * This test ensures that all getter are part of this testcase
     */
    public function testCompleteness()
    {
        # get all methods
        $methods = get_class_methods(VentilationUnit::class);

        # get functionname out of dataProvider
        $provider = $this->provider();
        $providerGetter = [];
        foreach ($provider as $item) {
            $providerGetter[] = $item[0];
        }

        # whitelist of functions to be ignored
        $whitelist = ['__construct'];

        # compare
        $this->assertEquals(array_diff($methods, $providerGetter), $whitelist);
    }

}
