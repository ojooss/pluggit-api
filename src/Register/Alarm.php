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
        return match ($value) {
            0 => Translation::singleton()->translate('alarm-0'),
            1 => Translation::singleton()->translate('alarm-1'),
            2 => Translation::singleton()->translate('alarm-2'),
            3 => Translation::singleton()->translate('alarm-3'),
            4 => Translation::singleton()->translate('alarm-4'),
            5 => Translation::singleton()->translate('alarm-5'),
            6 => Translation::singleton()->translate('alarm-6'),
            7 => Translation::singleton()->translate('alarm-7'),
            8 => Translation::singleton()->translate('alarm-8'),
            9 => Translation::singleton()->translate('alarm-9'),
            10 => Translation::singleton()->translate('alarm-10'),
            11 => Translation::singleton()->translate('alarm-11'),
            12 => Translation::singleton()->translate('alarm-12'),
            13 => Translation::singleton()->translate('alarm-13'),
            14 => Translation::singleton()->translate('alarm-14'),
            15 => Translation::singleton()->translate('alarm-15'),
            default => Translation::singleton()->translate('alarm-unknown'),
        };
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
