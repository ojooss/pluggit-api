<?php

namespace PluggitApi\Register;

use Exception;
use PluggitApi\PHPModbus\PhpType;
use PluggitApi\Register;

class DateTime extends Register
{

    /**
     * @return int
     * @throws Exception
     */
    protected function readValue(): int
    {
        $registerData = $this->modbus->readMultipleRegisters(0, $this->reference, 20);
        $values = array_slice($registerData, 0, 4);
        return PhpType::bytes2unsignedInt($values);
    }

    /**
     * @param $value
     * @return string
     */
    protected function formatValue($value): string
    {
        $dateTime = \DateTime::createFromFormat('U', $value);
        return $dateTime->format($this->formatString);
    }
}
