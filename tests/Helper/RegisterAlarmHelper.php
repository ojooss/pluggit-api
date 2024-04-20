<?php

namespace PluggitApi\Tests\Helper;

use PluggitApi\Register\Alarm;

class RegisterAlarmHelper extends Alarm
{
    /**
     * @param $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }
}
