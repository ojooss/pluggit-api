<?php

namespace PluggitApi\Register;

use PluggitApi\Translation;

class ProgramState extends Numeric
{

    /**
     * @param $value
     * @return string
     * @throws \Exception
     */
    protected function formatValue($value)
    {
        switch($value) {
            case 0	: return Translation::singleton()->translate('program-state-standby');
            case 1	: return Translation::singleton()->translate('program-state-manual');
            case 2	: return Translation::singleton()->translate('program-state-demand');
            case 3	: return Translation::singleton()->translate('program-state-week-program');
            case 4	: return Translation::singleton()->translate('program-state-servo-flow');
            case 5	: return Translation::singleton()->translate('program-state-away');
            case 6	: return Translation::singleton()->translate('program-state-summer');
            case 7	: return Translation::singleton()->translate('program-state-di-override');
            case 8	: return Translation::singleton()->translate('program-state-hygrostat-override');
            case 9	: return Translation::singleton()->translate('program-state-fireplace');
            case 10	: return Translation::singleton()->translate('program-state-installer');
            case 11	: return Translation::singleton()->translate('program-state-fail-safe-1');
            case 12	: return Translation::singleton()->translate('program-state-fail-safe-2');
            case 13	: return Translation::singleton()->translate('program-state-fail-off');
            case 14	: return Translation::singleton()->translate('program-state-defrost-off');
            case 15	: return Translation::singleton()->translate('program-state-defrost');
            case 16	: return Translation::singleton()->translate('program-state-night');
            default	:
                return Translation::singleton()->translate('bypass-stats-unknown');
        }
    }

}
