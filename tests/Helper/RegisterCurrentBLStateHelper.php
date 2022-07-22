<?php

namespace PluggitApi\Tests\Helper;

use PluggitApi\Register\CurrentBLState;

class RegisterCurrentBLStateHelper extends CurrentBLState
{
    /**
     * @param $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}
