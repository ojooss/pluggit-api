<?php

namespace PluggitApi\Register;

use Exception;
use PluggitApi\Translation;

class UnitMode extends Numeric
{

    /**
     * @param $value
     * @return string
     * @throws Exception
     */
    protected function formatValue($value): string
    {
        switch ($value) {
            case 2: return Translation::singleton()->translate('unit-mode-demand');
            case 4: return Translation::singleton()->translate('unit-mode-manual');
            case 8: return Translation::singleton()->translate('unit-mode-program');
            case 16: return Translation::singleton()->translate('unit-mode-away');
            case 32784: return Translation::singleton()->translate('unit-mode-away-end');
            case 64: return Translation::singleton()->translate('unit-mode-fireplace');
            case 32832: return Translation::singleton()->translate('unit-mode-fireplace-end');
            case 2048: return Translation::singleton()->translate('unit-mode-summer');
            case 34816: return Translation::singleton()->translate('unit-mode-summer-end');
            case 128: return Translation::singleton()->translate('unit-mode-bypass-enable');
            case 32896: return Translation::singleton()->translate('unit-mode-bypass-disable');
            default:
                return Translation::singleton()->translate('unit-mode-unknown');
        }
    }


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

        switch ($value) {
            case 2: // DEMAND
            case 4: // MANUAL
            case 8: // WEEKPROGRAM
            case 16: // AWAY_START
            case 32784: // AWAY_END
            case 64: // FIREPLACE_START
            case 32832: // FIREPLACE_END
            case 2048: // SUMMER_START
            case 34816: // SUMMER_END
            case 128: // BYPASS_ENABLE
            case 32896: // BYPASS_DISABLE
                // -> value is OK
                break;
            default:
                throw new Exception(sprintf(Translation::singleton()->translate('unit-mode-invalid-value'), $value));
        }

        $this->modbus->writeMultipleRegister(0, $this->reference, [$value, 0], ["INT", "INT"]);

        // double check
        $checkValue = $this->readValue();
        if ($value == $checkValue) {
            $this->value = $value;
        }
        else {
            throw new Exception(sprintf(Translation::singleton()->translate('unit-mode-failed-set-value'), $value, $checkValue));
        }
    }

}
