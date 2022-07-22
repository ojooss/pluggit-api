<?php

namespace PluggitApi\Register;

use Exception;
use PluggitApi\Translation;

class FanSpeedLevel extends Numeric
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
        if ($value   < 0 || $value > 4) {
            throw new Exception(
                sprintf(Translation::singleton()->translate('fan-speed-invalid-value'), $value)
            );
        }

        // and write
        $this->modbus->writeMultipleRegister(0, $this->reference, [$value, 0], ["INT", "INT"]);

        // double check
        $checkValue = $this->readValue();
        if ($value == $checkValue) {
            $this->value = $value;
        } else {
            throw new Exception(
                sprintf(Translation::singleton()->translate('fan-speed-failed-set-value'), $value, $checkValue)
            );
        }
    }
}
