<?php

namespace PluggitApi;

use PHPModbus\ModbusMasterTcp;

abstract class Register
{
    /**
     * @var ModbusMasterTcp
     */
    protected $modbus;

    /**
     * @var string
     */
    protected $reference;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $formatString;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @return mixed
     */
    abstract protected function readValue();

    /**
     * @param $value
     * @return mixed
     */
    abstract protected function formatValue($value);

    /**
     * Register constructor.
     * @param ModbusMasterTcp $modbus
     * @param $reference
     * @param $address
     * @param $name
     * @param $description
     * @param $formatString
     * @throws \Exception
     */
    public function __construct(ModbusMasterTcp $modbus, $reference, $address, $name, $description, $formatString='%s')
    {
        $this->modbus = $modbus;
        $this->reference = $reference;
        $this->address = $address;
        $this->name = $name;
        $this->description = $description;
        $this->formatString = Translation::singleton()->translate($formatString);
    }

    /**
     * Used for test purpose
     *
     * @param ModbusMasterTcp $modbus
     */
    public function setModbus(ModbusMasterTcp $modbus) {
        $this->modbus = $modbus;
    }


    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getValue($formatted=false, $force=false)
    {
        if (null === $this->value || $force) {
            $this->value = $this->readValue();
        }

        if ($formatted) {
            return $this->formatValue($this->value);
        }
        else {
            return $this->value;
        }
    }

    /**
     * By default register is readonly
     *
     * @return bool
     */
    public function isWriteAble()
    {
        return false;
    }
}
