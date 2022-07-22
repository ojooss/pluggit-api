<?php

namespace PluggitApi\Register;

use Exception;
use PluggitApi\PHPModbus\PhpType;
use PluggitApi\Register;

class Numeric extends Register
{

    /**
     * @return float
     * @throws Exception
     */
    protected function readValue(): float
    {
        $registerData = $this->modbus->readMultipleRegisters(0, $this->reference, 2);
        $values = array_slice($registerData, 0, 4);
        return PhpType::bytes2unsignedInt($values);
    }

    /**
     * @param $value
     * @return string
     */
    protected function formatValue($value): string
    {
        return sprintf($this->formatString, $value);
    }
}
