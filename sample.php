<?php

require_once __DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

$ipAddress = '192.168.21.5';

$ventilationUnit = new \PluggitApi\VentilationUnit($ipAddress, 'de');

echo PHP_EOL;
echo "--- PluggitApi ---".PHP_EOL;
echo PHP_EOL;

echo "Current date and time: ".$ventilationUnit->getCurrentDateTime(true).' ('.$ventilationUnit->getCurrentDateTime().')'.PHP_EOL;
echo "Start exploitation date: ".$ventilationUnit->getStartExploitationDate(true).' ('.$ventilationUnit->getStartExploitationDate().')'.PHP_EOL;
echo "Worktime: ".$ventilationUnit->getWorkTime(true).' ('.$ventilationUnit->getWorkTime().')'.PHP_EOL;
echo "Outdoor temperature: ".$ventilationUnit->getOutdoorTemperature(true).' ('.$ventilationUnit->getOutdoorTemperature().')'.PHP_EOL;
echo "Supply temperature: ".$ventilationUnit->getSupplyTemperature(true).' ('.$ventilationUnit->getSupplyTemperature().')'.PHP_EOL;
echo "Extract temperature: ".$ventilationUnit->getExtractTemperature(true).' ('.$ventilationUnit->getExtractTemperature().')'.PHP_EOL;
echo "Exhaust temperature: ".$ventilationUnit->getExhaustTemperature(true).' ('.$ventilationUnit->getExhaustTemperature().')'.PHP_EOL;
echo "Fan-1 speed: ".$ventilationUnit->getFanSpeed1(true).' ('.$ventilationUnit->getFanSpeed1().')'.PHP_EOL;
echo "Fan-2 speed: ".$ventilationUnit->getFanSpeed2(true).' ('.$ventilationUnit->getFanSpeed2().')'.PHP_EOL;
echo "Fan speed level: ".$ventilationUnit->getFanSpeedLevel(true).' ('.$ventilationUnit->getFanSpeedLevel().')'.PHP_EOL;
echo "Filter default time: ".$ventilationUnit->getFilterDefaultTime(true),' ('.$ventilationUnit->getFilterDefaultTime().')'.PHP_EOL;
echo "Filter remaining time: ".$ventilationUnit->getFilterDefaultTime(true).' ('.$ventilationUnit->getFilterDefaultTime().')'.PHP_EOL;
echo "Bypass state: ".$ventilationUnit->getBypassState(true).' ('.$ventilationUnit->getBypassState().')'.PHP_EOL;
echo "Bypass temperature min: ".$ventilationUnit->getBypassTemperatureMin(true).' ('.$ventilationUnit->getBypassTemperatureMin().')'.PHP_EOL;
echo "Bypass temperature max: ".$ventilationUnit->getBypassTemperatureMax(true).' ('.$ventilationUnit->getBypassTemperatureMax().')'.PHP_EOL;
echo "Bypass manual timeout: ".$ventilationUnit->getBypassManualTimeout(true).' ('.$ventilationUnit->getBypassManualTimeout().')'.PHP_EOL;
echo "Preheater duty cycle: ".$ventilationUnit->getPreheaterDutyCycle(true).' ('.$ventilationUnit->getPreheaterDutyCycle().')'.PHP_EOL;
echo "Unit mode: ".$ventilationUnit->getUnitMode(true).' ('.$ventilationUnit->getUnitMode().')'.PHP_EOL;
echo "CurrentBLState: ".$ventilationUnit->getCurrentBLState(true).' ('.$ventilationUnit->getCurrentBLState().')'.PHP_EOL;
echo "WeekProgram: ".$ventilationUnit->getWeekProgram(true).' ('.$ventilationUnit->getWeekProgram(false).')'.PHP_EOL;

echo PHP_EOL;

$oldValue = $ventilationUnit->getFanSpeedLevel(false);
$ventilationUnit->setFanSpeedLevel(1);
echo "Set FanSpeedLevel to: ".$ventilationUnit->getFanSpeedLevel(true).PHP_EOL;
$ventilationUnit->setFanSpeedLevel($oldValue);

$oldValue = $ventilationUnit->getFilterDefaultTime();
$ventilationUnit->setFilterDefaultTime(77);
echo "Set FilterDefaultTime to: ".$ventilationUnit->getFilterDefaultTime(true).PHP_EOL;
$ventilationUnit->setFilterDefaultTime($oldValue);

$oldValue = $ventilationUnit->getUnitMode(false);
$ventilationUnit->setUnitMode(16);
echo "Set UnitMode to: ".$ventilationUnit->getUnitMode(true).PHP_EOL;
$ventilationUnit->setUnitMode($oldValue);


$oldValue = $ventilationUnit->getWeekProgram(false);
$ventilationUnit->setWeekProgram(5);
echo "Set WeekProgram to: ".$ventilationUnit->getWeekProgram(true).PHP_EOL;
$ventilationUnit->setWeekProgram($oldValue);

echo PHP_EOL;
