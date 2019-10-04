<?php

namespace PluggitApi\Register;

use PHPModbus\PhpType;
use PluggitApi\Register;

class DateTime extends Register
{

    /**
     * @return int
     */
    protected function readValue()
    {
        $registerData = $this->modbus->readMultipleRegisters(0, $this->register, 20);
        $values = array_slice($registerData, 0, 4);
        return PhpType::bytes2unsignedInt($values);
    }

    /**
     * @param $value
     * @return string
     */
    protected function formatValue($value)
    {
        return date($this->formatString, $value);
    }

}
