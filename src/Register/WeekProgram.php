<?php

namespace PluggitApi\Register;

use Exception;
use PluggitApi\Translation;

class WeekProgram extends Numeric
{

    /**
     * overwrite register method
     *
     * @return bool
     */
    public function isWriteAble(): bool
    {
        return true;
    }

    /**
     * @param $value
     * @throws Exception
     */
    public function writeValue($value)
    {
        //validate
        if ($value   < 0 || $value > 10) {
            throw new Exception(sprintf(Translation::singleton()->translate('week-program-invalid-value'), $value));
        }

        // and write
        $this->modbus->writeMultipleRegister(0, $this->reference, [$value, 0], ["INT", "INT"]);

        // double check
        $checkValue = $this->readValue();
        if ($value == $checkValue) {
            $this->value = $value;
        }
        else {
            throw new Exception(sprintf(Translation::singleton()->translate('week-program-failed-set-value'), $value, $checkValue));
        }
    }

}
