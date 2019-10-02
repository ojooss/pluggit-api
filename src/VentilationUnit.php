<?php

namespace PluggitApi;

use PHPModbus\ModbusMasterTcp;
use PluggitApi\Register\DateTime;
use PluggitApi\Register\Temperature;

class VentilationUnit
{

    /**
     * @var Register[]
     */
    protected $register = [];

    /**
     * VentilationUnit constructor.
     * @param $ipAddress
     */
    public function __construct($ipAddress)
    {
        $modbus = new ModbusMasterTcp($ipAddress);

        $this->register['CurrentDateTime']       = new DateTime($modbus, '108','40109', 'prmDateTime', 'Current date/time');
        $this->register['StartExploitationDate'] = new DateTime($modbus, '668','40669', 'prmStartExploitationDateStamp', 'date/time of the system start');

        $this->register['OutdoorTemperature']    = new Temperature($modbus, '132', '40133', 'prmRamIdxT1', 'Outdoor temperature T1');
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