<?php

namespace PluggitApi\Tests\Helper;

use PluggitApi\Register\UnitMode;

class RegisterUnitModeHelper extends UnitMode
{
    /**
     * @param $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}
