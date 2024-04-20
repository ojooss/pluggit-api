<?php

namespace PluggitApi;

use Exception;
use PluggitApi\PHPModbus\ModbusMasterTcp;

abstract class Register
{

    public const REGISTER_START_ADDRESS = 40001;

    /**
     * @var ModbusMasterTcp
     */
    protected ModbusMasterTcp $modbus;

    /**
     * @var int
     */
    protected int $reference;

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
    protected mixed $value = null;

    /**
     * @return mixed
     */
    abstract protected function readValue(): mixed;

    /**
     * @param $value
     * @return mixed
     */
    abstract protected function formatValue($value): mixed;

    /**
     * Register constructor.
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
        $this->reference = ($address - self::REGISTER_START_ADDRESS);
        $this->name = $name;
        $this->description = $description;
        $this->formatString = Translation::singleton()->translate($formatString);
    }

    /**
     * Used for test purpose
     */
    public function setModbus(ModbusMasterTcp $modbus): void
    {
        $this->modbus = $modbus;
    }


    public function getValue(bool $formatted = false, bool $force = false): mixed
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
