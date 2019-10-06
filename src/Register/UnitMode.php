<?php

namespace PluggitApi\Register;

use PluggitApi\Translation;

class UnitMode extends Numeric
{

    /**
     * @param $value
     * @return string
     * @throws \Exception
     */
    protected function formatValue($value)
    {
        switch ($value) {
            case 2: return Translation::singleton()->translate('current-bl-state-demand');
            case 4: return Translation::singleton()->translate('current-bl-state-manual');
            case 8: return Translation::singleton()->translate('current-bl-state-week-program');
            case 16: return Translation::singleton()->translate('current-bl-state-away');
            #case 32784: // PLUGGIT_MODBUS_SET_PrmRamIdxUnitMode_AWAY_END:

            case 64: return Translation::singleton()->translate('current-bl-state-fireplace');
            #case 32832: // PLUGGIT_MODBUS_SET_PrmRamIdxUnitMode_FIREPLACE_END:

            case 2048: return Translation::singleton()->translate('current-bl-state-summer');
            #case 34816: // PLUGGIT_MODBUS_SET_PrmRamIdxUnitMode_SUMMER_END:

            case 128: return Translation::singleton()->translate('current-bl-state-bypass');
            #case 32896: // PLUGGIT_MODBUS_SET_PrmRamIdxUnitMode_BYPASS_DISABLE:

            default:
                return Translation::singleton()->translate('current-bl-state-unknown');
        }
    }


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

        switch ($value) {
            case 2: // PLUGGIT_MODBUS_SET_PrmRamIdxUnitMode_DEMAND:
            case 4: // PLUGGIT_MODBUS_SET_PrmRamIdxUnitMode_MANUAL:
            case 8: // PLUGGIT_MODBUS_SET_PrmRamIdxUnitMode_WEEKPROGRAM:
            case 16: // PLUGGIT_MODBUS_SET_PrmRamIdxUnitMode_AWAY_START	:
            case 32784: // PLUGGIT_MODBUS_SET_PrmRamIdxUnitMode_AWAY_END:

            case 64: // PLUGGIT_MODBUS_SET_PrmRamIdxUnitMode_FIREPLACE_START:
            case 32832: // PLUGGIT_MODBUS_SET_PrmRamIdxUnitMode_FIREPLACE_END:

            case 2048: // PLUGGIT_MODBUS_SET_PrmRamIdxUnitMode_SUMMER_START:
            case 34816: // PLUGGIT_MODBUS_SET_PrmRamIdxUnitMode_SUMMER_END:

            case 128: // PLUGGIT_MODBUS_SET_PrmRamIdxUnitMode_BYPASS_ENABLE:
            case 32896: // PLUGGIT_MODBUS_SET_PrmRamIdxUnitMode_BYPASS_DISABLE:
                // -> value is OK
                break;
            default:
                throw new \Exception('current-bl-state-invalid-value');
        }

        //validate
        if ($value < 0 || $value > 16) {
            throw new \Exception(Translation::singleton()->translate('invalid-current-bl-state-value'));
        }
        // and write
        $this->modbus->writeMultipleRegister(0, $this->reference, [$value, 0], ["INT", "INT"]);

        // double check
        $checkValue = $this->readValue();
        if ($value == $checkValue) {
            $this->value = $value;
        }
        else {
            throw new \Exception(Translation::singleton()->translate('failed-set-current-bl-state-value'));
        }
    }

}
