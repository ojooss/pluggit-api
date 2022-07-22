<?php

namespace PluggitApi\Tests\Helper;

use Exception;
use PluggitApi\PHPModbus\ModbusMasterTcp;

class ModbusMasterMock extends ModbusMasterTcp
{
    /**
     * @param int $unitId
     * @param int $reference
     * @param int $quantity
     * @return array
     * @throws Exception
     */
    public function readMultipleRegisters(int $unitId, int $reference, int $quantity): array
    {
        $mockFile = __DIR__.DIRECTORY_SEPARATOR.
            '..'.DIRECTORY_SEPARATOR.
            'data'.DIRECTORY_SEPARATOR.
            $reference.'.mock';
        if (file_exists($mockFile)) {
            $json = file_get_contents($mockFile);
            return json_decode($json);
        } else {
            throw new Exception('No mock data available for ' . $reference);
        }
    }

    /**
     * @param int $unitId
     * @param int $reference
     * @param array $data
     * @param array $dataTypes
     * @return bool
     */
    public function writeMultipleRegister(int $unitId, int $reference, array $data, array $dataTypes): bool
    {
        return true;
    }
}
