<?php

namespace PluggitApi;

use Exception;
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
     * @param int $reference
     * @param int $address
     * @param string $name
     * @param string $description
     * @param string $formatString
     * @throws Exception
     */
    public function __construct(ModbusMasterTcp $modbus, int $reference, int $address, string $name, string $description, string $formatString='%s')
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
    public function getValue(bool $formatted=false, bool $force=false)
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
    public function isWriteAble(): bool
    {
        return false;
    }
}
