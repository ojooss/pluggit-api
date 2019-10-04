<?php

require_once __DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

$ipAddress = '192.168.21.5';

$ventilationUnit = new \PluggitApi\VentilationUnit($ipAddress, 'de');
echo PHP_EOL;
echo "--- PluggitApi ---".PHP_EOL;

echo "Current date and time: ".$ventilationUnit->getCurrentDateTime(true).' ('.$ventilationUnit->getCurrentDateTime().')'.PHP_EOL;
echo "Start exploitation date: ".$ventilationUnit->getStartExploitationDate(true).' ('.$ventilationUnit->getStartExploitationDate().')'.PHP_EOL;
echo "Worktime: ".$ventilationUnit->getWorkTime(true).' ('.$ventilationUnit->getWorkTime().')'.PHP_EOL;
echo "Outdoor temperature: ".$ventilationUnit->getOutdoorTemperature(true).' ('.$ventilationUnit->getOutdoorTemperature().')'.PHP_EOL;
echo "Supply temperature: ".$ventilationUnit->getSupplyTemperature(true).' ('.$ventilationUnit->getSupplyTemperature().')'.PHP_EOL;
echo "Extract temperature: ".$ventilationUnit->getExtractTemperature(true).' ('.$ventilationUnit->getExtractTemperature().')'.PHP_EOL;
echo "Exhaust temperature: ".$ventilationUnit->getExhaustTemperature(true).' ('.$ventilationUnit->getExhaustTemperature().')'.PHP_EOL;

echo "Actual program: ".$ventilationUnit->getCurrentProgram(true).' ('.$ventilationUnit->getCurrentProgram().')'.PHP_EOL;

echo "Fan-1 speed: ".$ventilationUnit->getFanSpeed1(true).' ('.$ventilationUnit->getFanSpeed1().')'.PHP_EOL;
echo "Fan-2 speed: ".$ventilationUnit->getFanSpeed2(true).' ('.$ventilationUnit->getFanSpeed2().')'.PHP_EOL;
echo "Fan speed level: ".$ventilationUnit->getFanSpeedLevel(true).' ('.$ventilationUnit->getFanSpeedLevel().')'.PHP_EOL;
echo "Filter remaining time: ".$ventilationUnit->getFilterRemainingTime(true).' ('.$ventilationUnit->getFilterRemainingTime().')'.PHP_EOL;

echo "Bypass state: ".$ventilationUnit->getBypassState(true).' ('.$ventilationUnit->getBypassState().')'.PHP_EOL;
echo "Bypass temperature min: ".$ventilationUnit->getBypassTemperatureMin(true).' ('.$ventilationUnit->getBypassTemperatureMin().')'.PHP_EOL;
echo "Bypass temperature max: ".$ventilationUnit->getBypassTemperatureMax(true).' ('.$ventilationUnit->getBypassTemperatureMax().')'.PHP_EOL;
echo "Bypass manual timeout: ".$ventilationUnit->getBypassManualTimeout(true).' ('.$ventilationUnit->getBypassManualTimeout().')'.PHP_EOL;

echo PHP_EOL;
