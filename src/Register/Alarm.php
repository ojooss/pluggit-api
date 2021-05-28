<?php

namespace PluggitApi\Register;

use Exception;
use PluggitApi\Translation;

class Alarm extends Numeric
{

    /**
     * @param $value
     * @return string
     * @throws Exception
     */
    protected function formatValue($value): string
    {
        switch ($value) {
            case 0: return Translation::singleton()->translate('alarm-0');
            case 1: return Translation::singleton()->translate('alarm-1');
            case 2: return Translation::singleton()->translate('alarm-2');
            case 3: return Translation::singleton()->translate('alarm-3' );
            case 4: return Translation::singleton()->translate('alarm-4');
            case 5: return Translation::singleton()->translate('alarm-5');
            case 6: return Translation::singleton()->translate('alarm-6');
            case 7: return Translation::singleton()->translate('alarm-7');
            case 8: return Translation::singleton()->translate('alarm-8');
            case 9: return Translation::singleton()->translate('alarm-9');
            case 10: return Translation::singleton()->translate('alarm-10');
            case 11: return Translation::singleton()->translate('alarm-11');
            case 12: return Translation::singleton()->translate('alarm-12');
            case 13: return Translation::singleton()->translate('alarm-13');
            case 14: return Translation::singleton()->translate('alarm-14');
            case 15: return Translation::singleton()->translate('alarm-15');
            default:
                return Translation::singleton()->translate('alarm-unknown');
        }
    }


    /**
     * overwrite register method
     *
     * @return bool
     */
    public function isWriteAble(): bool
    {
        return false;
    }

}
