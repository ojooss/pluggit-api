<?php

namespace PluggitApi\Register;

use PHPModbus\ModbusMasterTcp;
use PHPModbus\PhpType;

class Floating extends Numeric
{

    /**
     * @var integer
     */
    protected $decimals;

    /**
     * Numeric constructor.
     * @param ModbusMasterTcp $modbus
     * @param $register
     * @param $address
     * @param $name
     * @param $description
     * @param $formatString
     * @param $decimals
     * @throws \Exception
     */
    public function __construct(ModbusMasterTcp $modbus, $register, $address, $name, $description, $formatString='%s', int $decimals=0)
    {
        parent::__construct($modbus, $register, $address, $name, $description, $formatString);
        $this->decimals = $decimals;
    }

    /**
     * @return float
     */
    protected function readValue()
    {
        $registerData = $this->modbus->readMultipleRegisters(0, $this->register, 2);
        $values = array_slice($registerData, 0, 4);
        return PhpType::bytes2float($values);
    }

    /**
     * @param $value
     * @return string
     */
    protected function formatValue($value)
    {
        $locale = localeconv();
        $number = number_format(
            $value,
            $this->decimals,
            $locale['decimal_point'],
            $locale['thousands_sep']
        );

        return sprintf($this->formatString, $number);
    }

}
