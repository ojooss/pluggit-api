<?php /** @noinspection PhpUnused */

namespace PluggitApi;

use Exception;
use PHPModbus\ModbusMasterTcp;
use PluggitApi\Register\BypassState;
use PluggitApi\Register\CurrentBLState;
use PluggitApi\Register\DateTime;
use PluggitApi\Register\FanSpeedLevel;
use PluggitApi\Register\FilterDefaultTime;
use PluggitApi\Register\Floating;
use PluggitApi\Register\Numeric;
use PluggitApi\Register\UnitMode;
use PluggitApi\Register\WeekProgram;

class VentilationUnit
{

    /**
     * @var Register[]
     */
    protected $register = [];

    /**
     * VentilationUnit constructor.
     * @param $ipAddress
     * @param $lang
     * @throws Exception
     */
    public function __construct($ipAddress, $lang)
    {
        // init Translations
        Translation::singleton($lang);

        // init Modbus
        $modbus = new ModbusMasterTcp($ipAddress);

        // genereate register
        $this->register['CurrentDateTime'] = new DateTime($modbus, '108', '40109', 'prmDateTime', 'Current date/time', 'date-time-format');
        $this->register['StartExploitationDate'] = new DateTime($modbus, '668', '40669', 'prmStartExploitationDateStamp', 'date/time of the system start', 'date-time-format');
        $this->register['WorkTime'] = new Numeric($modbus, '624', '40625', 'prmWorkTime', 'Work time of system, in hours', '%s hours');
        $this->register['OutdoorTemperature'] = new Floating($modbus, '132', '40133', 'prmRamIdxT1', 'Outdoor temperature T1', '%s °C');
        $this->register['SupplyTemperature'] = new Floating($modbus, '134', '40135', 'prmRamIdxT2', 'Supply temperature T2', '%s °C');
        $this->register['ExtractTemperature'] = new Floating($modbus, '136', '40137', 'prmRamIdxT3', 'Extract temperature T3', '%s °C');
        $this->register['ExhaustTemperature'] = new Floating($modbus, '138', '40139', 'prmRamIdxT4', 'Exhaust temperature T4', '%s °C');
        $this->register['FanSpeedLevel'] = new FanSpeedLevel($modbus, '324', '40325', 'prmRomIdxSpeedLevel', 'Speed level of Fans', '%s');
        $this->register['FanSpeed1'] = new Floating($modbus, '100', '40101', 'prmHALTaho1', 'Fan1 rpm', '%s rpm');
        $this->register['FanSpeed2'] = new Floating($modbus, '102', '40103', 'prmHALTaho2', 'Fan2 rpm', '%s rpm');
        $this->register['FilterDefaultTime'] = new FilterDefaultTime($modbus, '556', '40555', 'prmFilterDefaultTime', 'Filter Lifetime (Days)', '%s days');
        $this->register['FilterRemainingTime'] = new Numeric($modbus, '554', '40555', 'prmFilterRemainingTime', 'Remaining time of the Filter Lifetime', '%s days');
        $this->register['BypassTemperatureMin'] = new Floating($modbus, '444', '40445', 'prmBypassTmin', 'Min temperature for outdoor air', '%s °C');
        $this->register['BypassTemperatureMax'] = new Floating($modbus, '446', '40447', 'prmBypassTmax', 'Max temperature for extract air', '%s °C');
        $this->register['BypassManualTimeout'] = new Numeric($modbus, '264', '40265', 'prmRamIdxBypassManualTimeout', 'Manual bypass duration in minutes', '%s minutes');
        $this->register['BypassState'] = new BypassState($modbus, '198', '40199', 'prmRamIdxBypassActualState', 'Bypass state');
        $this->register['PreheaterDutyCycle'] = new Numeric($modbus, '160', '40161', 'prmPreheaterDutyCycle', 'Power of Preheater in %', '%s %%');
        $this->register['CurrentBLState'] = new CurrentBLState($modbus, '472', '40473', 'prmCurrentBLState', 'Current unit mode');
        $this->register['UnitMode'] = new UnitMode($modbus, '168', '40169', 'prmRamIdxUnitMode', 'Active Unit mode');
        $this->register['WeekProgram'] = new WeekProgram($modbus, (40467-40001), '40467', 'prmNumOfWeekProgram', 'Number of the Active Week Program (for Week Program mode)');
    }

    /**
     * @param string $function
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    protected function getValueByFunction(string $function, bool $formatted = false, bool $force=false)
    {
        $index = str_replace('get', '', $function);
        return $this->register[$index]->getValue($formatted, $force);
    }


    /************************* GETTER SECTION ************************/

    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getCurrentDateTime(bool $formatted = false, bool $force=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted, $force);
    }

    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getStartExploitationDate(bool $formatted = false, bool $force=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted, $force);
    }

    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getWorkTime(bool $formatted = false, bool $force=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted, $force);
    }

    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getOutdoorTemperature(bool $formatted = false, bool $force=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted, $force);
    }

    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getSupplyTemperature(bool $formatted = false, bool $force=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted, $force);
    }

    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getExtractTemperature(bool $formatted = false, bool $force=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted, $force);
    }

    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getExhaustTemperature(bool $formatted = false, bool $force=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted, $force);
    }

    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getFanSpeed1(bool $formatted = false, bool $force=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted, $force);
    }

    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getFanSpeed2(bool $formatted = false, bool $force=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted, $force);
    }

    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getFanSpeedLevel(bool $formatted = false, bool $force=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted, $force);
    }

    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getFilterDefaultTime(bool $formatted = false, bool $force=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted, $force);
    }

    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getFilterRemainingTime(bool $formatted = false, bool $force=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted, $force);
    }

    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getBypassTemperatureMin(bool $formatted = false, bool $force=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted, $force);
    }

    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getBypassTemperatureMax(bool $formatted = false, bool $force=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted, $force);
    }

    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getBypassManualTimeout(bool $formatted = false, bool $force=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted, $force);
    }

    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getBypassState(bool $formatted = false, bool $force=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted, $force);
    }

    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getUnitMode(bool $formatted = false, bool $force=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted, $force);
    }

    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getPreheaterDutyCycle(bool $formatted = false, bool $force=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted, $force);
    }

    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getCurrentBLState(bool $formatted = false, bool $force=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted, $force);
    }

    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getWeekProgram(bool $formatted = false, bool $force=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted, $force);
    }


    /************************* SETTER SECTION *************************/

    /**
     * @param $value
     * @return void
     * @throws Exception
     */
    public function setUnitMode($value)
    {
        /** @var UnitMode $register */
        $register = $this->register['UnitMode'];
        $register->writeValue($value);
    }

    /**
     * @param $value
     * @return void
     * @throws Exception
     */
    public function setFanSpeedLevel($value)
    {
        /** @var FanSpeedLevel $register */
        $register = $this->register['FanSpeedLevel'];
        $register->writeValue($value);
    }

    /**
     * @param $value
     * @return void
     * @throws Exception
     */
    public function setFilterDefaultTime($value)
    {
        /** @var FilterDefaultTime $register */
        $register = $this->register['FilterDefaultTime'];
        $register->writeValue($value);
    }

    /**
     * @param $value
     * @return void
     * @throws Exception
     */
    public function setWeekProgram($value)
    {
        /** @var WeekProgram $register */
        $register = $this->register['WeekProgram'];
        $register->writeValue($value);
    }

}
