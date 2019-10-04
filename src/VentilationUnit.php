<?php

namespace PluggitApi;

use PHPModbus\ModbusMasterTcp;
use PluggitApi\Register\BypassState;
use PluggitApi\Register\DateTime;
use PluggitApi\Register\Numeric;
use PluggitApi\Register\ProgramState;

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

        // @TODO: setter
        $this->register['CurrentDateTime']       = new DateTime($modbus, '108','40109', 'prmDateTime', 'Current date/time', 'date-time-format');
        $this->register['StartExploitationDate'] = new DateTime($modbus, '668','40669', 'prmStartExploitationDateStamp', 'date/time of the system start', 'date-time-format');
        $this->register['WorkTime'] = new Numeric($modbus, '624','40625', 'prmWorkTime', 'Work time of system, in hours', '%s hours');

        $this->register['OutdoorTemperature']    = new Numeric($modbus, '132', '40133', 'prmRamIdxT1', 'Outdoor temperature T1', '%s °C');
        $this->register['SupplyTemperature']     = new Numeric($modbus, '134', '40135', 'prmRamIdxT2', 'Supply temperature T2', '%s °C');
        $this->register['ExtractTemperature']    = new Numeric($modbus, '136', '40137', 'prmRamIdxT3', 'Extract temperature T3', '%s °C');
        $this->register['ExhaustTemperature']    = new Numeric($modbus, '138', '40139', 'prmRamIdxT4', 'Exhaust temperature T4', '%s °C');

        // @TODO: setter
        $this->register['FanSpeedLevel']    = new Numeric($modbus, '324', '40325', 'prmRomIdxSpeedLevel', 'Speed level of Fans', '%s');

        $this->register['FanSpeedLevel']    = new Numeric($modbus, '100', '40101', 'prmHALTaho1', 'Fan1 rpm', '%s rpm');
        $this->register['FanSpeedLevel']    = new Numeric($modbus, '102', '40103', 'prmHALTaho2', 'Fan2 rpm', '%s rpm');

        // @TODO: setter
        $this->register['FilterRemainingTime']    = new Numeric($modbus, '554', '40555', 'prmFilterRemainingTime', 'Remaining time of the Filter Lifetime', '%s days');

        $this->register['BypassTemperatureMin']    = new Numeric($modbus, '444', '40445', 'prmBypassTmin', 'Min temperature for outdoor air', '%s');
        $this->register['BypassTemperatureMax']    = new Numeric($modbus, '446', '40447', 'prmBypassTmax', 'Max temperature for extract air', '%s');
        $this->register['BypassManualTimeout']    = new Numeric($modbus, '264', '40265', 'prmRamIdxBypassManualTimeout', 'Manual bypass duration in minutes', '%s');
#        $this->register['BypassActualState']    = new BypassState($modbus, '198', '40199', 'prmRamIdxBypassActualState', 'Bypass state');
        //																0	Closed		0x0000
        //																1	In process	0x0001
        //																32	Closing		0x0020
        //																64	Opening		0x0040
        //																255	Opened		0x00FF

 #       $this->register['prmCurrentBLState']    = new ProgramState($modbus, '472', '40473', 'prmCurrentBLState', 'Current unit mode');
        //																0	Standby
        //																1	Manual
        //																2	Demand
        //																3	Week program
        //																4	Servo-flow
        //																5	Away
        //																6	Summer
        //																7	DI Override
        //																8	Hygrostat override
        //																9	Fireplace
        //																10	Installer
        //																11	Fail Safe 1
        //																12	Fail Safe 2
        //																13	Fail Off
        //																14	Defrost Off
        //																15	Defrost
        //																16	Night


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

    public function getOutdoorTemperature($formatted=false)
    {
        return $this->getValueByFunction(__FUNCTION__, $formatted);
    }

}