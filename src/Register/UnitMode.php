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
        return match ((int)$value) {
            2 => Translation::singleton()->translate('unit-mode-demand'),
            4 => Translation::singleton()->translate('unit-mode-manual'),
            8 => Translation::singleton()->translate('unit-mode-program'),
            16 => Translation::singleton()->translate('unit-mode-away'),
            64 => Translation::singleton()->translate('unit-mode-fireplace'),
            128 => Translation::singleton()->translate('unit-mode-bypass-enable'),
            2048 => Translation::singleton()->translate('unit-mode-summer'),
            32784 => Translation::singleton()->translate('unit-mode-away-end'),
            32832 => Translation::singleton()->translate('unit-mode-fireplace-end'),
            32896 => Translation::singleton()->translate('unit-mode-bypass-disable'),
            34816 => Translation::singleton()->translate('unit-mode-summer-end'),
            default => Translation::singleton()->translate('unit-mode-unknown'),
        };
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
    public function writeValue($value): void
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
                throw new Exception(
                    sprintf(Translation::singleton()->translate('unit-mode-invalid-value'), $value)
                );
        }

        $this->modbus->writeMultipleRegister(0, $this->reference, [$value, 0], ["INT", "INT"]);

        // double check
        $checkValue = $this->readValue();
        if ($value == $checkValue) {
            $this->value = $value;
        } else {
            throw new Exception(
                sprintf(Translation::singleton()->translate('unit-mode-failed-set-value'), $value, $checkValue)
            );
        }
    }
}
