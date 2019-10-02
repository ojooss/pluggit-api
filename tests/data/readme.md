generate mock files by adding the following line to ModbusMaster::readMultipleRegisters

file_put_contents(__DIR__.'/../../../../tests/data/'.$reference.'.mock', json_encode($receivedData));
