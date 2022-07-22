<?php

namespace PluggitApi\Tests\Helper;

use PluggitApi\Register;

class RegisterHelper extends Register
{
    /**
     * @return int
     */
    protected function readValue(): int
    {
        return 42;
    }

    /**
     * @param $value
     * @return string
     */
    protected function formatValue($value): string
    {
        return "0-8-15";
    }
}
