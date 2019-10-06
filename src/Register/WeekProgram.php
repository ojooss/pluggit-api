<?php

namespace PluggitApi\Register;

use PluggitApi\Translation;

class WeekProgram extends Numeric
{

    /**
     * overwrite register method
     *
     * @return bool
     */
    public function isWriteAble()
    {
        return true;
    }

    /**
     * @param $value
     * @throws \Exception
     */
    public function writeValue($value)
    {
        //validate
        if ($value   < 1 || $value > 11) {
            throw new \Exception(Translation::singleton()->translate('invalid-week-program-value'));
        }

        // and write
        $this->modbus->writeMultipleRegister(0, $this->reference, [$value, 0], ["INT", "INT"]);

        // double check
        $checkValue = $this->getValue();
        if ($value == $checkValue) {
            $this->value = $value;
        }
        else {
            throw new \Exception(Translation::singleton()->translate('failed-set-week-program-value'));
        }
    }

}
