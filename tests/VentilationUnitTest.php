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
        parent::__construct($ipAddress, 'en');

        $modbus = new ModbusMasterMock($ipAddress);

        foreach ($this->register as $item) {
            $item->setModbus($modbus);
        }
    }
}

final class VentilationUnitTest extends TestCase
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
    public function provider(): array
    {
        return [
            ['getCurrentDateTime', 1570052849, '10/02/2019 21:47:29'],
            ['getStartExploitationDate', 1506794566, '09/30/2017 18:02:46'],
            ['getWorkTime', 45601, '45601 hours'],
            ['getOutdoorTemperature', 17.556928634643555, '18 °C'],
            ['getSupplyTemperature', 18.13029670715332, '18 °C'],
            ['getExtractTemperature', 23.359394073486328, '23 °C'],
            ['getExhaustTemperature', 19.572856903076172, '20 °C'],
            ['getFanSpeed1', 1614.3931884765625, '1614 rpm'],
            ['getFanSpeed2', 1564.848876953125, '1565 rpm'],
            ['getFanSpeedLevel', 3, '3'],
            ['getFilterDefaultTime', 90, '90 days'],
            ['getBypassState', 255, 'opened'],
            ['getBypassTemperatureMin', 15.0, '15 °C'],
            ['getBypassTemperatureMax', 21.0, '21 °C'],
            ['getBypassManualTimeout', 60, '60 minutes'],
            ['getUnitMode', 8, 'program'],
            ['getPreheaterDutyCycle', '0', '0 %'],
            ['getCurrentBLState', 1, 'manual'],
            ['getWeekProgram', 10, '10'],
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
        $whitelist = [];
        $whitelist[] = '__construct';
        $whitelist[] = 'setUnitMode';
        $whitelist[] = 'setFanSpeedLevel';
        $whitelist[] = 'setFilterDefaultTime';
        $whitelist[] = 'setWeekProgram';

        // little workaround to reset array_keys
        $diff = array_values(array_diff($methods, $providerGetter));

        # compare
        $this->assertEquals($diff, array_values($whitelist), 'Missing test for register::getter');
    }

}
