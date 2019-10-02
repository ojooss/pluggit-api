<?php

namespace PluggitApi\Register;

use PHPModbus\PhpType;
use PluggitApi\Register;

class Temperature extends Register
{

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
            0,
            $locale['decimal_point'],
            $locale['thousands_sep']
        );

        return $number." °C";
    }

}
