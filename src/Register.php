<?php

namespace PluggitApi;

use Exception;
use PluggitApi\PHPModbus\ModbusMasterTcp;

abstract class Register
{

    const REGISTER_START_ADDRESS = 40001;

    /**
     * @var ModbusMasterTcp
     */
    protected ModbusMasterTcp $modbus;

    /**
     * @var string
     */
    protected string $reference;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $description;

    /**
     * @var string
     */
    protected string $formatString;

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
     * @param int $address
     * @param string $name
     * @param string $description
     * @param string $formatString
     * @throws Exception
     */
    public function __construct(
        ModbusMasterTcp $modbus,
        int $address,
        string $name,
        string $description,
        string $formatString = '%s'
    ) {
        $this->modbus = $modbus;
        $this->reference = ($address-self::REGISTER_START_ADDRESS);
        $this->name = $name;
        $this->description = $description;
        $this->formatString = Translation::singleton()->translate($formatString);
    }

    /**
     * Used for test purpose
     *
     * @param ModbusMasterTcp $modbus
     */
    public function setModbus(ModbusMasterTcp $modbus)
    {
        $this->modbus = $modbus;
    }


    /**
     * @param bool $formatted
     * @param bool $force
     * @return mixed
     */
    public function getValue(bool $formatted = false, bool $force = false)
    {
        if (null === $this->value || $force) {
            $this->value = $this->readValue();
        }

        if ($formatted) {
            return $this->formatValue($this->value);
        } else {
            return $this->value;
        }
    }

    /**
     * By default, register is readonly
     *
     * @return bool
     */
    public function isWriteAble(): bool
    {
        return false;
    }
}
