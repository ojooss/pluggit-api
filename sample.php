<?php

require_once __DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

$ipAddress = '192.168.21.5';

$ventilationUnit = new \PluggitApi\VentilationUnit($ipAddress);
echo PHP_EOL;
echo "--- PluggitApi ---".PHP_EOL;
echo "Current DateTime: ".$ventilationUnit->getCurrentDateTime(true).' ('.$ventilationUnit->getCurrentDateTime().')'.PHP_EOL;
echo "DateTime of system start: ".$ventilationUnit->getStartExploitationDate(true).' ('.$ventilationUnit->getStartExploitationDate().')'.PHP_EOL;
echo "Outdoor temperature: ".$ventilationUnit->getOutdoorTemperature(true).' ('.$ventilationUnit->getOutdoorTemperature().')'.PHP_EOL;
echo PHP_EOL;
