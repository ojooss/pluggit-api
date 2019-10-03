generate mock files by adding the following line to ModbusMaster::readMultipleRegisters

$mockFile = __DIR__.'/../../../../tests/data/'.$reference.'.mock';
if (!file_exists($mockFile)) file_put_contents($mockFile, json_encode($receivedData));
