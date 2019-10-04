<?php

namespace PluggitApi\Register;

use PHPModbus\ModbusMasterTcp;
use PHPModbus\PhpType;
use PluggitApi\Register;

class Numeric extends Register
{
    /**
     * Numeric constructor.
     * @param ModbusMasterTcp $modbus
     * @param $register
     * @param $address
     * @param $name
     * @param $description
     * @param $formatString
     * @throws \Exception
     */
    public function __construct(ModbusMasterTcp $modbus, $register, $address, $name, $description, $formatString='%s')
    {
        parent::__construct($modbus, $register, $address, $name, $description, $formatString);
    }

    /**
     * @return float
     */
    protected function readValue()
    {
        $registerData = $this->modbus->readMultipleRegisters(0, $this->register, 2);
        $values = array_slice($registerData, 0, 4);
        return PhpType::bytes2unsignedInt($values);
    }

    /**
     * @param $value
     * @return string
     */
    protected function formatValue($value)
    {
        return sprintf($this->formatString, $value);
    }

}
