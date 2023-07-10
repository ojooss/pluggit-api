<?php

namespace PluggitApi\Tests\Helper;

use PluggitApi\Register\BypassState;

class RegisterBypassStateHelper extends BypassState
{
    /**
     * @param $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }
}
