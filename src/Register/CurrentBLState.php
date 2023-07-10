<?php

namespace PluggitApi\Register;

use Exception;
use PluggitApi\Translation;

class CurrentBLState extends Numeric
{

    /**
     * @param $value
     * @return string
     * @throws Exception
     */
    protected function formatValue($value): string
    {
        return match ($value) {
            0 => Translation::singleton()->translate('current-bl-state-standby'),
            1 => Translation::singleton()->translate('current-bl-state-manual'),
            2 => Translation::singleton()->translate('current-bl-state-demand'),
            3 => Translation::singleton()->translate('current-bl-state-week-program'),
            4 => Translation::singleton()->translate('current-bl-state-servo-flow'),
            5 => Translation::singleton()->translate('current-bl-state-away'),
            6 => Translation::singleton()->translate('current-bl-state-summer'),
            7 => Translation::singleton()->translate('current-bl-state-di-override'),
            8 => Translation::singleton()->translate('current-bl-state-hygrostat-override'),
            9 => Translation::singleton()->translate('current-bl-state-fireplace'),
            10 => Translation::singleton()->translate('current-bl-state-installer'),
            11 => Translation::singleton()->translate('current-bl-state-fail-safe-1'),
            12 => Translation::singleton()->translate('current-bl-state-fail-safe-2'),
            13 => Translation::singleton()->translate('current-bl-state-fail-off'),
            14 => Translation::singleton()->translate('current-bl-state-defrost-off'),
            15 => Translation::singleton()->translate('current-bl-state-defrost'),
            16 => Translation::singleton()->translate('current-bl-state-night'),
            default => Translation::singleton()->translate('current-bl-state-unknown'),
        };
    }
}
