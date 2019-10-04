<?php

namespace PluggitApi\Register;

use PHPModbus\PhpType;
use PluggitApi\Translation;


class BypassState extends Floating
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
     * @throws \Exception
     */
    protected function formatValue($value)
    {
        switch($value) {
            case 0:
                return Translation::singleton()->translate('bypass-stats-closed');
            case 1:
                return Translation::singleton()->translate('bypass-stats-in-process');
            case 32:
                return Translation::singleton()->translate('bypass-stats-closing');
            case 64:
                return Translation::singleton()->translate('bypass-stats-opening');
            case 255:
                return Translation::singleton()->translate('bypass-stats-opened');
            default	:
                return Translation::singleton()->translate('bypass-stats-unknown');
        }
    }

}
