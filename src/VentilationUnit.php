<?php

namespace PluggitApi;

use PHPModbus\ModbusMasterTcp;
use PluggitApi\Register\BypassState;
use PluggitApi\Register\CurrentBLState;
use PluggitApi\Register\CurrentDateTime;
use PluggitApi\Register\DateTime;
use PluggitApi\Register\FanSpeedLevel;
use PluggitApi\Register\FilterDefaultTime;
use PluggitApi\Register\Floating;
use PluggitApi\Register\Numeric;
use PluggitApi\Register\UnitMode;

class VentilationUnit
{

    /**
     * @var Register[]
     */
    protected $register = [];

    /**
     * VentilationUnit constructor.
     * @param $ipAddress
     * @throws \Exception
     */
    public function __construct($ipAddress, $lang)
    {
        // init Translations
        Translation::singleton($lang);

        // init Modbus
        $modbus = new ModbusMasterTcp($ipAddress);

        // genereate register
        $this->register['CurrentDateTime']       = new DateTime(         $modbus, '108', '40109', 'prmDateTime', 'Current date/time', 'date-time-format');
        $this->register['StartExploitationDate'] = new DateTime(         $modbus, '668', '40669', 'prmStartExploitationDateStamp', 'date/time of the system start', 'date-time-format');
        $this->register['WorkTime']              = new Numeric(          $modbus, '624', '40625', 'prmWorkTime', 'Work time of system, in hours', '%s hours');
        $this->register['OutdoorTemperature']    = new Floating(         $modbus, '132', '40133', 'prmRamIdxT1', 'Outdoor temperature T1', '%s °C');
        $this->register['SupplyTemperature']     = new Floating(         $modbus, '134', '40135', 'prmRamIdxT2', 'Supply temperature T2', '%s °C');
        $this->register['ExtractTemperature']    = new Floating(         $modbus, '136', '40137', 'prmRamIdxT3', 'Extract temperature T3', '%s °C');
        $this->register['ExhaustTemperature']    = new Floating(         $modbus, '138', '40139', 'prmRamIdxT4', 'Exhaust temperature T4', '%s °C');
        $this->register['FanSpeedLevel']         = new FanSpeedLevel(    $modbus, '324', '40325', 'prmRomIdxSpeedLevel', 'Speed level of Fans', '%s');
        $this->register['FanSpeed1']             = new Floating(         $modbus, '100', '40101', 'prmHALTaho1', 'Fan1 rpm', '%s rpm');
        $this->register['FanSpeed2']             = new Floating(         $modbus, '102', '40103', 'prmHALTaho2', 'Fan2 rpm', '%s rpm');
        $this->register['FilterDefaultTime']     = new FilterDefaultTime($modbus, '556', '40555', 'prmFilterDefaultTime', 'Filter Lifetime (Days)', '%s days');
        $this->register['FilterRemainingTime']   = new Numeric(          $modbus, '554', '40555', 'prmFilterRemainingTime', 'Remaining time of the Filter Lifetime', '%s days');
        $this->register['BypassTemperatureMin']  = new Floating(         $modbus, '444', '40445', 'prmBypassTmin', 'Min temperature for outdoor air', '%s °C');
        $this->register['BypassTemperatureMax']  = new Floating(         $modbus, '446', '40447', 'prmBypassTmax', 'Max temperature for extract air', '%s °C');
        $this->register['BypassManualTimeout']   = new Numeric(          $modbus, '264', '40265', 'prmRamIdxBypassManualTimeout', 'Manual bypass duration in minutes', '%s minutes');
        $this->register['BypassState']           = new BypassState(      $modbus, '198', '40199', 'prmRamIdxBypassActualState', 'Bypass state');
        $this->register['PreheaterDutyCycle']    = new Numeric(          $modbus, '160', '40161', 'prmPreheaterDutyCycle', 'Power of Preheater in %', '%s %%');
        $this->register['CurrentBLState']        = new CurrentBLState(   $modbus, '472', '40473', 'prmCurrentBLState', 'Current unit mode');
        $this->register['UnitMode']              = new UnitMode(         $modbus, '168', '40169', 'prmRamIdxUnitMode', 'Active Unit mode');
    }

    /**
     * @param string $function
     * @param bool $formatted
     * @return mixed
     */
    protected function getValueByFunction($function, $formatted=false)
    {
        $index = str_replace('get', '', $function);
        return $this->register[$index]->getValue($formatted);
    }



    /************************* GETTER SECTION *************************/

    public function getCurrentDateTime($formatted=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted);
    }

    public function getStartExploitationDate($formatted=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted);
    }

    public function getWorkTime($formatted=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted);
    }

    public function getOutdoorTemperature($formatted=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted);
    }


    public function getSupplyTemperature($formatted=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted);
    }

    public function getExtractTemperature($formatted=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted);
    }

    public function getExhaustTemperature($formatted=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted);
    }

    public function getFanSpeed1($formatted=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted);
    }

    public function getFanSpeed2($formatted=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted);
    }

    public function getFanSpeedLevel($formatted=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted);
    }

    public function getFilterDefaultTime($formatted=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted);
    }

    public function getBypassTemperatureMin($formatted=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted);
    }

    public function getBypassTemperatureMax($formatted=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted);
    }

    public function getBypassManualTimeout($formatted=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted);
    }

    public function getBypassState($formatted=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted);
    }

    public function getUnitMode($formatted=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted);
    }

    public function getPreheaterDutyCycle($formatted=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted);
    }

    public function getCurrentBLState($formatted=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted);
    }


    /************************* SETTER SECTION *************************/

    public function setUnitMode($value)
    {
        /** @var UnitMode $register */
        $register = $this->register['UnitMode'];
        $register->writeValue($value);
    }

    public function setFanSpeedLevel($value)
    {
        /** @var FanSpeedLevel $register */
        $register = $this->register['FanSpeedLevel'];
        $register->writeValue($value);
    }

    public function setFilterDefaultTime($value)
    {
        /** @var FilterDefaultTime $register */
        $register = $this->register['FilterDefaultTime'];
        $register->writeValue($value);
    }


}