<?php

use PHPModbus\ModbusMasterTcp;

class ModbusMasterMock extends ModbusMasterTcp
{
    public function readMultipleRegisters($unitId, $reference, $quantity)
    {
        $json = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.$reference.'.mock');
        return json_decode($json);
    }

    public function writeMultipleRegister($unitId, $reference, $data, $dataTypes)
    {
        return true;
    }

}