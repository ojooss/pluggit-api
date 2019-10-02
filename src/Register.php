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
    protected $register;

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
     * @var mixed
     */
    protected $value;

    /**
     * Register constructor.
     * @param ModbusMasterTcp $modbus
     * @param $register
     * @param $address
     * @param $name
     * @param $description
     */
    public function __construct(ModbusMasterTcp $modbus, $register, $address, $name, $description)
    {
        $this->modbus = $modbus;
        $this->register = $register;
        $this->address = $address;
        $this->name = $name;
        $this->description = $description;
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
     * @return mixed
     */
    abstract protected function readValue();

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
     * @param $value
     * @return mixed
     */
    abstract protected function formatValue($value);

}
