<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PluggitApi\Translation;
use PluggitApi\VentilationUnit;

require_once __DIR__.DIRECTORY_SEPARATOR.'ModbusMasterMock.php';

class VentilationUnitHelper extends VentilationUnit
{
    public function __construct($ipAddress)
    {
        parent::__construct($ipAddress, 'de');

        $modbus = new ModbusMasterMock($ipAddress);

        foreach ($this->register as $item) {
            $item->setModbus($modbus);
        }
    }
}

final class VentilationUnitTest extends TestCase
{

    public static function setUpBeforeClass(): void
    {
        // init with test language
        Translation::singleton('de');
    }

    /**
     * @return array
     */
    public function provider()
    {
        return [
            ['getCurrentDateTime', 1570052849, '02.10.2019 21:47:29'],
            ['getStartExploitationDate', 1506794566, '30.09.2017 18:02:46'],
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
