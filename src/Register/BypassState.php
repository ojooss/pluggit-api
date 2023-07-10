<?php

namespace PluggitApi\Register;

use Exception;
use PluggitApi\Translation;

class BypassState extends Numeric
{

    /**
     * @param $value
     * @return string
     * @throws Exception
     */
    protected function formatValue($value): string
    {
        return match ($value) {
            0 => Translation::singleton()->translate('bypass-stats-closed'),
            1 => Translation::singleton()->translate('bypass-stats-in-process'),
            32 => Translation::singleton()->translate('bypass-stats-closing'),
            64 => Translation::singleton()->translate('bypass-stats-opening'),
            255 => Translation::singleton()->translate('bypass-stats-opened'),
            default => Translation::singleton()->translate('bypass-stats-unknown'),
        };
    }
}
