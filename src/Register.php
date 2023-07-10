<?php

namespace PluggitApi;

use Exception;
use PluggitApi\PHPModbus\ModbusMasterTcp;

abstract class Register
{

    public const REGISTER_START_ADDRESS = 40001;

    protected int $reference;

    protected mixed $value = null;

    abstract protected function readValue(): mixed;

    abstract protected function formatValue($value): mixed;

    /**
     * @throws Exception
     */
    public function __construct(
        protected ModbusMasterTcp $modbus,
        protected int $address,
        protected string $name,
        protected string $description,
        protected string $formatString = '%s'
    ) {
        $this->reference = ($address - self::REGISTER_START_ADDRESS);
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
     */
    public function isWriteAble(): bool
    {
        return false;
    }
}
