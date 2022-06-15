<?php

namespace PluggitApi\Register;

use Exception;
use PHPModbus\ModbusMasterTcp;
use PHPModbus\PhpType;

class Floating extends Numeric
{

    /**
     * @var integer
     */
    protected int $decimals;

    /**
     * Numeric constructor.
     * @param ModbusMasterTcp $modbus
     * @param int $address
     * @param string $name
     * @param string $description
     * @param string $formatString
     * @param int $decimals
     * @throws Exception
     */
    public function __construct(ModbusMasterTcp $modbus, int $address, string $name, string $description, string $formatString='%s', int $decimals=0)
    {
        parent::__construct($modbus, $address, $name, $description, $formatString);
        $this->decimals = $decimals;
    }

    /**
     * @return float
     */
    protected function readValue(): float
    {
        $registerData = $this->modbus->readMultipleRegisters(0, $this->reference, 2);
        $values = array_slice($registerData, 0, 4);
        return PhpType::bytes2float($values);
    }

    /**
     * @param $value
     * @return string
     */
    protected function formatValue($value): string
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
