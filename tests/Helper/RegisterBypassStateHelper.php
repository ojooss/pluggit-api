<?php

namespace PluggitApi\Tests\Helper;

use PluggitApi\Register\BypassState;

class RegisterBypassStateHelper extends BypassState
{
    /**
     * @param $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}
