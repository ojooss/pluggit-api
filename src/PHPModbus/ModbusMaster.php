<?php
/** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

namespace PluggitApi\PHPModbus;

use Exception;

/**
 * Phpmodbus Copyright (c) 2004, 2013 Jan Krakora
 *
 * This source file is subject to the "PhpModbus license" that is bundled
 * with this package in the file license.txt.
 *
 *
 * @copyright  Copyright (c) 2004, 2013 Jan Krakora
 * @license    PhpModbus license
 * @category   Phpmodbus
 * @tutorial   Phpmodbus.pkg
 * @package    Phpmodbus
 * @version    $id$
 *
 */


/**
 * ModbusMaster
 *
 * This class deals with the MODBUS master
 *
 * Implemented MODBUS master functions:
 *   - FC  1: read coils
 *   - FC  2: read input discretes
 *   - FC  3: read multiple registers
 *   - FC  4: read multiple input registers
 *   - FC  5: write single coil
 *   - FC  6: write single register
 *   - FC 15: write multiple coils
 *   - FC 16: write multiple registers
 *   - FC 22: mask write register
 *   - FC 23: read write registers
 *
 * @author     Jan Krakora
 * @copyright  Copyright (c) 2004, 2013 Jan Krakora
 * @package    Phpmodbus
 *
 */
class ModbusMaster
{
    /** @var resource Communication socket */
    private $sock;

    /** @var string Modbus device IP address */
    public string $host = "192.168.1.1";

    /** @var int gateway port */
    public int $port = 502;

    /** @var string (optional) client IP address */
    public string $client = ""; // TODO explanation?

    /** @var int client port */
    public int $client_port = 502;

    /** @var string ModbusMaster status messages (echo for debugging) */
    public string $status = '';

    /** @var float Total response timeout (seconds, decimals allowed) */
    public float $timeout_sec = 3;

    /** @var float Socket read timeout (seconds, decimals allowed) */
    public float $socket_read_timeout_sec = 0.3; // 300 ms

    /** @var float Socket write timeout (seconds, decimals allowed) */
    public float $socket_write_timeout_sec = 1;

    /** @var int Endianness codding (0 = little endian = 0, 1 = big endian) */
    public int $endianness = IecType::LITTLE_ENDIAN; //

    /** @var string Socket protocol (TCP, UDP) */
    public string $socket_protocol = "UDP";

    /**
     * ModbusMaster
     *
     * This is the constructor that defines {@link $host} IP address of the object.
     *
     * @param String $host     An IP address of a Modbus TCP device. E.g. "192.168.1.1"
     * @param String $protocol Socket protocol (TCP, UDP)
     */
    public function __construct(string $host, string $protocol)
    {
        $this->socket_protocol = $protocol;
        $this->host = $host;
    }

    /**
     * __toString
     *
     * Magic method
     */
    public function __toString()
    {
        return "<pre>" . $this->status . "</pre>";
    }

    /**
     * Convert float in seconds to array
     *
     * @param float $secs
     * @return array {sec: ..., usec: ...}
     */
    private function secsToSecUsecArray(float $secs): array
    {
        $remainder = $secs - floor($secs);

        return [
            'sec' => round($secs - $remainder),
            'usec' => round($remainder * 1e6),
        ];
    }

    /**
     * connect
     *
     * Connect the socket
     *
     * @return void
     * @throws Exception
     */
    private function connect(): void
    {
        // Create a protocol specific socket
        if ($this->socket_protocol == "TCP") {
            // TCP socket
            $this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        } elseif ($this->socket_protocol == "UDP") {
            // UDP socket
            $this->sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        } else {
            throw new Exception("Unknown socket protocol, should be 'TCP' or 'UDP'");
        }
        // Bind the client socket to a specific local port
        if (strlen($this->client) > 0) {
            $result = socket_bind($this->sock, $this->client, $this->client_port);
            if ($result === false) {
                throw new Exception("socket_bind() failed. Reason: " .
                    socket_strerror(socket_last_error($this->sock)));
            } else {
                $this->status .= "Bound" . PHP_EOL;
            }
        }

        // Socket settings (send/write timeout)
        $writeTimeout = $this->secsToSecUsecArray($this->socket_write_timeout_sec);
        socket_set_option($this->sock, SOL_SOCKET, SO_SNDTIMEO, $writeTimeout);

        // Connect the socket
        $result = @socket_connect($this->sock, $this->host, $this->port);
        if ($result === false) {
            throw new Exception("socket_connect() failed. Reason: " .
                socket_strerror(socket_last_error($this->sock)));
        } else {
            $this->status .= "Connected" . PHP_EOL;
        }
    }

    /**
     * disconnect
     *
     * Disconnect the socket
     */
    private function disconnect()
    {
        socket_close($this->sock);
        $this->status .= "Disconnected" . PHP_EOL;
    }

    /**
     * send
     *
     * Send the packet via Modbus
     *
     * @param string $packet
     */
    private function send(string $packet)
    {
        socket_write($this->sock, $packet, strlen($packet));
        $this->status .= "Send" . PHP_EOL;
    }

    /**
     * rec
     *
     * Receive data from the socket
     *
     * @return string|null
     * @throws Exception
     */
    private function rec(): ?string
    {
        socket_set_nonblock($this->sock);
        $readsocks[] = $this->sock;
        $writesocks = null;
        $exceptsocks = null;
        $rec = "";
        $totalReadTimeout = $this->timeout_sec;
        $lastAccess = microtime(true);
        $readStart = microtime(true);
        $readTout = $this->secsToSecUsecArray($this->socket_read_timeout_sec);
        while (false !== socket_select($readsocks, $writesocks, $exceptsocks, $readTout['sec'], $readTout['usec'])) {
            if (in_array($this->sock, $readsocks)) {
                if (@socket_recv($this->sock, $rec, 2000, 0)) { // read max 2000 bytes
                    $this->status .= "Data received " . PHP_EOL;
                    return $rec;
                }
                $lastAccess = microtime(true);
                if (microtime(true) - $readStart > $totalReadTimeout) {
                    throw new Exception(
                        "Read timed out, last error: " . socket_strerror(socket_last_error($this->sock))
                    );
                }
            } else {
                $timeSpentWaiting = microtime(true) - $lastAccess;
                if ($timeSpentWaiting >= $totalReadTimeout) {
                    throw new Exception(
                        "Watchdog time expired [ $totalReadTimeout sec ]!!! " .
                        "Connection to $this->host:$this->port is not established."
                    );
                }
            }
            $readsocks[] = $this->sock;
        }

        return null;
    }

    /**
     * responseCode
     *
     * Check the Modbus response code
     *
     * @param string $packet
     * @return bool
     * @throws Exception
     */
    private function responseCode(string $packet): bool
    {
        if ((ord($packet[7]) & 0x80) > 0) {
            // failure code
            $failure_code = ord($packet[8]);
            // failure code strings
            $failures = array(
                0x01 => "ILLEGAL FUNCTION",
                0x02 => "ILLEGAL DATA ADDRESS",
                0x03 => "ILLEGAL DATA VALUE",
                0x04 => "SLAVE DEVICE FAILURE",
                0x05 => "ACKNOWLEDGE",
                0x06 => "SLAVE DEVICE BUSY",
                0x08 => "MEMORY PARITY ERROR",
                0x0A => "GATEWAY PATH UNAVAILABLE",
                0x0B => "GATEWAY TARGET DEVICE FAILED TO RESPOND",
            );
            // get failure string
            if (key_exists($failure_code, $failures)) {
                $failure_str = $failures[$failure_code];
            } else {
                $failure_str = "UNDEFINED FAILURE CODE";
            }
            // exception response
            throw new Exception("Modbus response error code: $failure_code ($failure_str)");
        } else {
            $this->status .= "Modbus response error code: NOERROR" . PHP_EOL;
            return true;
        }
    }

    /**
     * readCoils
     *
     * Modbus function FC 1(0x01) - Read Coils
     *
     * Reads {@link $quantity} of Coils (boolean) from reference
     * {@link $reference} of a memory of a Modbus device given by
     * {@link $unitId}.
     *
     * @param int $unitId
     * @param int $reference
     * @param int $quantity
     * @return bool[]
     * @throws Exception
     */
    public function readCoils(int $unitId, int $reference, int $quantity): array
    {
        $this->status .= "readCoils: START" . PHP_EOL;
        // connect
        $this->connect();
        // send FC 1
        $packet = $this->readCoilsPacketBuilder($unitId, $reference, $quantity);
        $this->status .= $this->printPacket($packet);
        $this->send($packet);
        // receive response
        $rpacket = $this->rec();
        $this->status .= $this->printPacket($rpacket);
        // parse packet
        $receivedData = $this->readCoilsParser($rpacket, $quantity);
        // disconnect
        $this->disconnect();
        $this->status .= "readCoils: DONE" . PHP_EOL;
        // return
        return $receivedData;
    }

    /**
     * fc1
     *
     * Alias to {@link readCoils} method
     *
     * @param int $unitId
     * @param int $reference
     * @param int $quantity
     * @return bool[]
     * @throws Exception
     */
    public function fc1(int $unitId, int $reference, int $quantity): array
    {
        return $this->readCoils($unitId, $reference, $quantity);
    }

    /**
     * readCoilsPacketBuilder
     *
     * FC1 packet builder - read coils
     *
     * @param int $unitId
     * @param int $reference
     * @param int $quantity
     * @return string
     */
    private function readCoilsPacketBuilder(int $unitId, int $reference, int $quantity): string
    {
        $dataLen = 0;
        // build data section
        $buffer1 = "";
        // build body
        $buffer2 = IecType::iecBYTE(1);              // FC 1 = 1(0x01)
        // build body - read section
        $buffer2 .= IecType::iecINT($reference);      // refnumber = 12288
        $buffer2 .= IecType::iecINT($quantity);       // quantity
        $dataLen += 5;
        // build header
        $buffer3 = IecType::iecINT(rand(0, 65000));   // transaction ID
        $buffer3 .= IecType::iecINT(0);               // protocol ID
        $buffer3 .= IecType::iecINT($dataLen + 1);    // lenght
        $buffer3 .= IecType::iecBYTE($unitId);        //unit ID
        // return packet string
        return $buffer3 . $buffer2 . $buffer1;
    }

    /**
     * readCoilsParser
     *
     * FC 1 response parser
     *
     * @param string $packet
     * @param int $quantity
     * @return bool[]
     * @throws Exception
     */
    private function readCoilsParser(string $packet, int $quantity): array
    {
        $data = array();
        // check Response code
        $this->responseCode($packet);
        // get data from stream
        for ($i = 0; $i < ord($packet[8]); $i++) {
            $data[$i] = ord($packet[9 + $i]);
        }
        // get bool values to array
        $data_boolean_array = array();
        $di = 0;
        foreach ($data as $value) {
            for ($i = 0; $i < 8; $i++) {
                if ($di == $quantity) {
                    continue;
                }
                // get boolean value
                $v = ($value >> $i) & 0x01;
                // build boolean array
                if ($v == 0) {
                    $data_boolean_array[] = false;
                } else {
                    $data_boolean_array[] = true;
                }
                $di++;
            }
        }
        return $data_boolean_array;
    }

    /**
     * readInputDiscretes
     *
     * Modbus function FC 2(0x02) - Read Input Discretes
     *
     * Reads {@link $quantity} of Inputs (boolean) from reference
     * {@link $reference} of a memory of a Modbus device given by
     * {@link $unitId}.
     *
     * @param int $unitId
     * @param int $reference
     * @param int $quantity
     * @return bool[]
     * @throws Exception
     */
    public function readInputDiscretes(int $unitId, int $reference, int $quantity): array
    {
        $this->status .= "readInputDiscretes: START" . PHP_EOL;
        // connect
        $this->connect();
        // send FC 2
        $packet = $this->readInputDiscretesPacketBuilder($unitId, $reference, $quantity);
        $this->status .= $this->printPacket($packet);
        $this->send($packet);
        // receive response
        $rpacket = $this->rec();
        $this->status .= $this->printPacket($rpacket);
        // parse packet
        $receivedData = $this->readInputDiscretesParser($rpacket, $quantity);
        // disconnect
        $this->disconnect();
        $this->status .= "readInputDiscretes: DONE" . PHP_EOL;
        // return
        return $receivedData;
    }

    /**
     * fc2
     *
     * Alias to {@link readInputDiscretes} method
     *
     * @param int $unitId
     * @param int $reference
     * @param int $quantity
     * @return bool[]
     * @throws Exception
     */
    public function fc2(int $unitId, int $reference, int $quantity): array
    {
        return $this->readInputDiscretes($unitId, $reference, $quantity);
    }

    /**
     * readInputDiscretesPacketBuilder
     *
     * FC2 packet builder - read coils
     *
     * @param int $unitId
     * @param int $reference
     * @param int $quantity
     * @return string
     */
    private function readInputDiscretesPacketBuilder(int $unitId, int $reference, int $quantity): string
    {
        $dataLen = 0;
        // build data section
        $buffer1 = "";
        // build body
        $buffer2 = IecType::iecBYTE(2);              // FC 2 = 2(0x02)
        // build body - read section
        $buffer2 .= IecType::iecINT($reference);      // refnumber = 12288
        $buffer2 .= IecType::iecINT($quantity);       // quantity
        $dataLen += 5;
        // build header
        $buffer3 = IecType::iecINT(rand(0, 65000));   // transaction ID
        $buffer3 .= IecType::iecINT(0);               // protocol ID
        $buffer3 .= IecType::iecINT($dataLen + 1);    // lenght
        $buffer3 .= IecType::iecBYTE($unitId);        //unit ID
        // return packet string
        return $buffer3 . $buffer2 . $buffer1;
    }

    /**
     * readInputDiscretesParser
     *
     * FC 2 response parser, alias to FC 1 parser i.e. readCoilsParser.
     *
     * @param string $packet
     * @param int $quantity
     * @return bool[]
     * @throws Exception
     */
    private function readInputDiscretesParser(string $packet, int $quantity): array
    {
        return $this->readCoilsParser($packet, $quantity);
    }

    /**
     * readMultipleRegisters
     *
     * Modbus function FC 3(0x03) - Read Multiple Registers.
     *
     * This function reads {@link $quantity} of Words (2 bytes) from reference
     * {@link $referenceRead} of a memory of a Modbus device given by
     * {@link $unitId}.
     *
     *
     * @param int $unitId usually ID of Modbus device
     * @param int $reference Reference in the device memory to read data (e.g. in device WAGO 750-841, memory MW0
     *                       starts at address 12288).
     * @param int $quantity Amounth of the data to be read from device.
     * @return array Success flag or array of received data.
     * @throws Exception
     */
    public function readMultipleRegisters(int $unitId, int $reference, int $quantity): array
    {
        $this->status .= "readMultipleRegisters: START" . PHP_EOL;
        // connect
        $this->connect();
        // send FC 3
        $packet = $this->readMultipleRegistersPacketBuilder($unitId, $reference, $quantity);
        $this->status .= $this->printPacket($packet);
        $this->send($packet);
        // receive response
        $rpacket = $this->rec();
        $this->status .= $this->printPacket($rpacket);
        // parse packet
        $receivedData = $this->readMultipleRegistersParser($rpacket);
        // disconnect
        $this->disconnect();
        $this->status .= "readMultipleRegisters: DONE" . PHP_EOL;
        // return
        return $receivedData;
    }

    /**
     * fc3
     *
     * Alias to {@link readMultipleRegisters} method.
     *
     * @param int $unitId
     * @param int $reference
     * @param int $quantity
     * @return array
     * @throws Exception
     */
    public function fc3(int $unitId, int $reference, int $quantity): array
    {
        return $this->readMultipleRegisters($unitId, $reference, $quantity);
    }

    /**
     * readMultipleRegistersPacketBuilder
     *
     * Packet FC 3 builder - read multiple registers
     *
     * @param int $unitId
     * @param int $reference
     * @param int $quantity
     * @return string
     */
    private function readMultipleRegistersPacketBuilder(int $unitId, int $reference, int $quantity): string
    {
        $dataLen = 0;
        // build data section
        $buffer1 = "";
        // build body
        $buffer2 = IecType::iecBYTE(3);             // FC 3 = 3(0x03)
        // build body - read section
        $buffer2 .= IecType::iecINT($reference);  // refnumber = 12288
        $buffer2 .= IecType::iecINT($quantity);       // quantity
        $dataLen += 5;
        // build header
        $buffer3 = IecType::iecINT(rand(0, 65000));   // transaction ID
        $buffer3 .= IecType::iecINT(0);               // protocol ID
        $buffer3 .= IecType::iecINT($dataLen + 1);    // lenght
        $buffer3 .= IecType::iecBYTE($unitId);        //unit ID
        // return packet string
        return $buffer3 . $buffer2 . $buffer1;
    }

    /**
     * readMultipleRegistersParser
     *
     * FC 3 response parser
     *
     * @param string $packet
     * @return array
     * @throws Exception
     */
    private function readMultipleRegistersParser(string $packet): array
    {
        $data = array();
        // check Response code
        $this->responseCode($packet);
        // get data
        for ($i = 0; $i < ord($packet[8]); $i++) {
            $data[$i] = ord($packet[9 + $i]);
        }
        return $data;
    }

    /**
     * readMultipleInputRegisters
     *
     * Modbus function FC 4(0x04) - Read Multiple Input Registers.
     *
     * This function reads {@link $quantity} of Words (2 bytes) from reference
     * {@link $referenceRead} of a memory of a Modbus device given by
     * {@link $unitId}.
     *
     *
     * @param int $unitId usually ID of Modbus device
     * @param int $reference Reference in the device memory to read data.
     * @param int $quantity Amounth of the data to be read from device.
     * @return array Success flag or array of received data.
     * @throws Exception
     */
    public function readMultipleInputRegisters(int $unitId, int $reference, int $quantity): array
    {
        $this->status .= "readMultipleInputRegisters: START" . PHP_EOL;
        // connect
        $this->connect();
        // send FC 4
        $packet = $this->readMultipleInputRegistersPacketBuilder($unitId, $reference, $quantity);
        $this->status .= $this->printPacket($packet);
        $this->send($packet);
        // receive response
        $rpacket = $this->rec();
        $this->status .= $this->printPacket($rpacket);
        // parse packet
        $receivedData = $this->readMultipleInputRegistersParser($rpacket);
        // disconnect
        $this->disconnect();
        $this->status .= "readMultipleInputRegisters: DONE" . PHP_EOL;
        // return
        return $receivedData;
    }

    /**
     * fc4
     *
     * Alias to {@link readMultipleInputRegisters} method.
     *
     * @param int $unitId
     * @param int $reference
     * @param int $quantity
     * @return array
     * @throws Exception
     */
    public function fc4(int $unitId, int $reference, int $quantity): array
    {
        return $this->readMultipleInputRegisters($unitId, $reference, $quantity);
    }

    /**
     * readMultipleInputRegistersPacketBuilder
     *
     * Packet FC 4 builder - read multiple input registers
     *
     * @param int $unitId
     * @param int $reference
     * @param int $quantity
     * @return string
     */
    private function readMultipleInputRegistersPacketBuilder(int $unitId, int $reference, int $quantity): string
    {
        $dataLen = 0;
        // build data section
        $buffer1 = "";
        // build body
        $buffer2 = IecType::iecBYTE(4);                                                // FC 4 = 4(0x04)
        // build body - read section
        $buffer2 .= IecType::iecINT($reference);                                        // refnumber = 12288
        $buffer2 .= IecType::iecINT($quantity);                                         // quantity
        $dataLen += 5;
        // build header
        $buffer3 = IecType::iecINT(rand(0, 65000));                                     // transaction ID
        $buffer3 .= IecType::iecINT(0);                                                 // protocol ID
        $buffer3 .= IecType::iecINT($dataLen + 1);                                      // lenght
        $buffer3 .= IecType::iecBYTE($unitId);                                          // unit ID
        // return packet string
        return $buffer3 . $buffer2 . $buffer1;
    }

    /**
     * readMultipleInputRegistersParser
     *
     * FC 4 response parser
     *
     * @param string $packet
     * @return array
     * @throws Exception
     */
    private function readMultipleInputRegistersParser(string $packet): array
    {
        $data = array();
        // check Response code
        $this->responseCode($packet);
        // get data
        for ($i = 0; $i < ord($packet[8]); $i++) {
            $data[$i] = ord($packet[9 + $i]);
        }
        return $data;
    }

    /**
     * writeSingleCoil
     *
     * Modbus function FC5(0x05) - Write Single Register.
     *
     * This function writes {@link $data} single coil at {@link $reference} position of
     * memory of a Modbus device given by {@link $unitId}.
     *
     *
     * @param int $unitId usually ID of Modbus device
     * @param int $reference Reference in the device memory (e.g. in device WAGO 750-841, memory MW0 starts at
     *                         address 12288)
     * @param array $data value to be written (TRUE|FALSE).
     * @return bool Success flag
     * @throws Exception
     */
    public function writeSingleCoil(int $unitId, int $reference, array $data): bool
    {
        $this->status .= "writeSingleCoil: START" . PHP_EOL;
        // connect
        $this->connect();
        // send FC5
        $packet = $this->writeSingleCoilPacketBuilder($unitId, $reference, $data);
        $this->status .= $this->printPacket($packet);
        $this->send($packet);
        // receive response
        $rpacket = $this->rec();
        $this->status .= $this->printPacket($rpacket);
        // parse packet
        $this->writeSingleCoilParser($rpacket);
        // disconnect
        $this->disconnect();
        $this->status .= "writeSingleCoil: DONE" . PHP_EOL;
        return true;
    }

    /**
     * fc5
     *
     * Alias to {@link writeSingleCoil} method
     *
     * @param int $unitId
     * @param int $reference
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function fc5(int $unitId, int $reference, array $data): bool
    {
        return $this->writeSingleCoil($unitId, $reference, $data);
    }

    /**
     * writeSingleCoilPacketBuilder
     *
     * Packet builder FC5 - WRITE single register
     *
     * @param int   $unitId
     * @param int   $reference
     * @param array $data
     * @return string
     */
    private function writeSingleCoilPacketBuilder(int $unitId, int $reference, array $data): string
    {
        $dataLen = 0;
        // build data section
        $buffer1 = "";
        foreach ($data as $dataitem) {
            if ($dataitem) {
                $buffer1 = IecType::iecINT(0xFF00);
            } else {
                $buffer1 = IecType::iecINT(0x0000);
            }
        }
        $dataLen += 2;
        // build body
        $buffer2 = IecType::iecBYTE(5);             // FC5 = 5(0x05)
        $buffer2 .= IecType::iecINT($reference);      // refnumber = 12288
        $dataLen += 3;
        // build header
        $buffer3 = IecType::iecINT(rand(0, 65000));   // transaction ID
        $buffer3 .= IecType::iecINT(0);               // protocol ID
        $buffer3 .= IecType::iecINT($dataLen + 1);    // lenght
        $buffer3 .= IecType::iecBYTE($unitId);        //unit ID

        // return packet string
        return $buffer3 . $buffer2 . $buffer1;
    }

    /**
     * writeSingleCoilParser
     *
     * FC5 response parser
     *
     * @param string $packet
     * @return void
     * @throws Exception
     */
    private function writeSingleCoilParser(string $packet): void
    {
        $this->responseCode($packet);
    }

    /**
     * writeSingleRegister
     *
     * Modbus function FC6(0x06) - Write Single Register.
     *
     * This function writes {@link $data} single word value at {@link $reference} position of
     * memory of a Modbus device given by {@link $unitId}.
     *
     *
     * @param int $unitId usually ID of Modbus device
     * @param int $reference Reference in the device memory (e.g. in device WAGO 750-841, memory MW0 starts at
     *                         address 12288)
     * @param array $data Array of values to be written.
     * @return bool Success flag
     * @throws Exception
     */
    public function writeSingleRegister(int $unitId, int $reference, array $data): bool
    {
        $this->status .= "writeSingleRegister: START" . PHP_EOL;
        // connect
        $this->connect();
        // send FC6
        $packet = $this->writeSingleRegisterPacketBuilder($unitId, $reference, $data);
        $this->status .= $this->printPacket($packet);
        $this->send($packet);
        // receive response
        $rpacket = $this->rec();
        $this->status .= $this->printPacket($rpacket);
        // parse packet
        $this->writeSingleRegisterParser($rpacket);
        // disconnect
        $this->disconnect();
        $this->status .= "writeSingleRegister: DONE" . PHP_EOL;
        return true;
    }

    /**
     * fc6
     *
     * Alias to {@link writeSingleRegister} method
     *
     * @param int $unitId
     * @param int $reference
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function fc6(int $unitId, int $reference, array $data): bool
    {
        return $this->writeSingleRegister($unitId, $reference, $data);
    }

    /**
     * writeSingleRegisterPacketBuilder
     *
     * Packet builder FC6 - WRITE single register
     *
     * @param int   $unitId
     * @param int   $reference
     * @param array $data
     * @return string
     */
    private function writeSingleRegisterPacketBuilder(int $unitId, int $reference, array $data): string
    {
        $dataLen = 0;
        // build data section
        $buffer1 = "";
        foreach ($data as $dataitem) {
            $buffer1 .= IecType::iecINT($dataitem);   // register values x
            $dataLen += 2;
            break;
        }
        // build body
        $buffer2 = IecType::iecBYTE(6);             // FC6 = 6(0x06)
        $buffer2 .= IecType::iecINT($reference);      // refnumber = 12288
        $dataLen += 3;
        // build header
        $buffer3 = IecType::iecINT(rand(0, 65000));   // transaction ID
        $buffer3 .= IecType::iecINT(0);               // protocol ID
        $buffer3 .= IecType::iecINT($dataLen + 1);    // lenght
        $buffer3 .= IecType::iecBYTE($unitId);        //unit ID

        // return packet string
        return $buffer3 . $buffer2 . $buffer1;
    }

    /**
     * writeSingleRegisterParser
     *
     * FC6 response parser
     *
     * @param string $packet
     * @return void
     * @throws Exception
     */
    private function writeSingleRegisterParser(string $packet): void
    {
        $this->responseCode($packet);
    }

    /**
     * writeMultipleCoils
     *
     * Modbus function FC15(0x0F) - Write Multiple Coils
     *
     * This function writes {@link $data} array at {@link $reference} position of
     * memory of a Modbus device given by {@link $unitId}.
     *
     * @param int $unitId
     * @param int $reference
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function writeMultipleCoils(int $unitId, int $reference, array $data): bool
    {
        $this->status .= "writeMultipleCoils: START" . PHP_EOL;
        // connect
        $this->connect();
        // send FC15
        $packet = $this->writeMultipleCoilsPacketBuilder($unitId, $reference, $data);
        $this->status .= $this->printPacket($packet);
        $this->send($packet);
        // receive response
        $rpacket = $this->rec();
        $this->status .= $this->printPacket($rpacket);
        // parse packet
        $this->writeMultipleCoilsParser($rpacket);
        // disconnect
        $this->disconnect();
        $this->status .= "writeMultipleCoils: DONE" . PHP_EOL;
        return true;
    }

    /**
     * fc15
     *
     * Alias to {@link writeMultipleCoils} method
     *
     * @param int $unitId
     * @param int $reference
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function fc15(int $unitId, int $reference, array $data): bool
    {
        return $this->writeMultipleCoils($unitId, $reference, $data);
    }

    /**
     * writeMultipleCoilsPacketBuilder
     *
     * Packet builder FC15 - Write multiple coils
     *
     * @param int   $unitId
     * @param int   $reference
     * @param array $data
     * @return string
     */
    private function writeMultipleCoilsPacketBuilder(int $unitId, int $reference, array $data): string
    {
        $dataLen = 0;
        // build bool stream to the WORD array
        $data_word_stream = array();
        $data_word = 0;
        $shift = 0;
        for ($i = 0; $i < count($data); $i++) {
            if ((($i % 8) == 0) && ($i > 0)) {
                $data_word_stream[] = $data_word;
                $shift = 0;
                $data_word = 0;
            }
            $data_word |= (0x01 && $data[$i]) << $shift;
            $shift++;
        }
        $data_word_stream[] = $data_word;
        // show binary stream to status string
        foreach ($data_word_stream as $d) {
            $this->status .= sprintf("byte=b%08b" . PHP_EOL, $d);
        }
        // build data section
        $buffer1 = "";
        foreach ($data_word_stream as $dataitem) {
            $buffer1 .= IecType::iecBYTE($dataitem);   // register values x
            $dataLen += 1;
        }
        // build body
        $buffer2 = IecType::iecBYTE(15);             // FC 15 = 15(0x0f)
        $buffer2 .= IecType::iecINT($reference);      // refnumber = 12288
        $buffer2 .= IecType::iecINT(count($data));      // bit count
        $buffer2 .= IecType::iecBYTE((count($data) + 7) / 8);       // byte count
        $dataLen += 6;
        // build header
        $buffer3 = IecType::iecINT(rand(0, 65000));   // transaction ID
        $buffer3 .= IecType::iecINT(0);               // protocol ID
        $buffer3 .= IecType::iecINT($dataLen + 1);    // lenght
        $buffer3 .= IecType::iecBYTE($unitId);        // unit ID

        // return packet string
        return $buffer3 . $buffer2 . $buffer1;
    }

    /**
     * writeMultipleCoilsParser
     *
     * FC15 response parser
     *
     * @param string $packet
     * @return void
     * @throws Exception
     */
    private function writeMultipleCoilsParser(string $packet): void
    {
        $this->responseCode($packet);
    }

    /**
     * writeMultipleRegister
     *
     * Modbus function FC16(0x10) - Write Multiple Register.
     *
     * This function writes {@link $data} array at {@link $reference} position of
     * memory of a Modbus device given by {@link $unitId}.
     *
     *
     * @param int $unitId usually ID of Modbus device
     * @param int $reference Reference in the device memory (e.g. in device WAGO 750-841, memory MW0 starts at
     *                         address 12288)
     * @param array $data Array of values to be written.
     * @param array $dataTypes Array of types of values to be written. The array should consists of string "INT",
     *                         "DINT" and "REAL".
     * @return bool Success flag
     * @throws Exception
     */
    public function writeMultipleRegister(int $unitId, int $reference, array $data, array $dataTypes): bool
    {
        $this->status .= "writeMultipleRegister: START" . PHP_EOL;
        // connect
        $this->connect();
        // send FC16
        $packet = $this->writeMultipleRegisterPacketBuilder($unitId, $reference, $data, $dataTypes);
        $this->status .= $this->printPacket($packet);
        $this->send($packet);
        // receive response
        $rpacket = $this->rec();
        $this->status .= $this->printPacket($rpacket);
        // parse packet
        $this->writeMultipleRegisterParser($rpacket);
        // disconnect
        $this->disconnect();
        $this->status .= "writeMultipleRegister: DONE" . PHP_EOL;
        return true;
    }

    /**
     * fc16
     *
     * Alias to {@link writeMultipleRegister} method
     *
     * @param int $unitId
     * @param int $reference
     * @param array $data
     * @param array $dataTypes
     * @return bool
     * @throws Exception
     */
    public function fc16(int $unitId, int $reference, array $data, array $dataTypes): bool
    {
        return $this->writeMultipleRegister($unitId, $reference, $data, $dataTypes);
    }

    /**
     * writeMultipleRegisterPacketBuilder
     *
     * Packet builder FC16 - WRITE multiple register
     *     e.g.: 4dd90000000d0010300000030603e807d00bb8
     *
     * @param int $unitId
     * @param int $reference
     * @param array $data
     * @param array $dataTypes
     * @return string
     * @throws Exception
     */
    private function writeMultipleRegisterPacketBuilder(
        int $unitId,
        int $reference,
        array $data,
        array $dataTypes
    ): string {
        $dataLen = 0;
        // build data section
        $buffer1 = "";
        foreach ($data as $key => $dataitem) {
            if ($dataTypes[$key] == "INT") {
                $buffer1 .= IecType::iecINT($dataitem);   // register values x
                $dataLen += 2;
            } elseif ($dataTypes[$key] == "DINT") {
                $buffer1 .= IecType::iecDINT($dataitem, $this->endianness);   // register values x
                $dataLen += 4;
            } elseif ($dataTypes[$key] == "REAL") {
                $buffer1 .= IecType::iecREAL($dataitem, $this->endianness);   // register values x
                $dataLen += 4;
            } else {
                $buffer1 .= IecType::iecINT($dataitem);   // register values x
                $dataLen += 2;
            }
        }
        // build body
        $buffer2 = IecType::iecBYTE(16);             // FC 16 = 16(0x10)
        $buffer2 .= IecType::iecINT($reference);      // refnumber = 12288
        $buffer2 .= IecType::iecINT($dataLen / 2);        // word count
        $buffer2 .= IecType::iecBYTE($dataLen);     // byte count
        $dataLen += 6;
        // build header
        $buffer3 = IecType::iecINT(rand(0, 65000));   // transaction ID
        $buffer3 .= IecType::iecINT(0);               // protocol ID
        $buffer3 .= IecType::iecINT($dataLen + 1);    // lenght
        $buffer3 .= IecType::iecBYTE($unitId);        //unit ID

        // return packet string
        return $buffer3 . $buffer2 . $buffer1;
    }

    /**
     * writeMultipleRegisterParser
     *
     * FC16 response parser
     *
     * @param string $packet
     * @return void
     * @throws Exception
     */
    private function writeMultipleRegisterParser(string $packet): void
    {
        $this->responseCode($packet);
    }

    /**
     * maskWriteRegister
     *
     * Modbus function FC22(0x16) - Mask Write Register.
     *
     * This function alter single bit(s) at {@link $reference} position of
     * memory of a Modbus device given by {@link $unitId}.
     *
     * Result = (Current Contents AND And_Mask) OR (Or_Mask AND (NOT And_Mask))
     *
     * @param int $unitId usually ID of Modbus device
     * @param int $reference Reference in the device memory (e.g. in device WAGO 750-841, memory MW0 starts at address
     *                       12288)
     * @param int $andMask
     * @param int $orMask
     * @return bool Success flag
     * @throws Exception
     */
    public function maskWriteRegister(int $unitId, int $reference, int $andMask, int $orMask): bool
    {
        $this->status .= "maskWriteRegister: START" . PHP_EOL;
        // connect
        $this->connect();
        // send FC22
        $packet = $this->maskWriteRegisterPacketBuilder($unitId, $reference, $andMask, $orMask);
        $this->status .= $this->printPacket($packet);
        $this->send($packet);
        // receive response
        $rpacket = $this->rec();
        $this->status .= $this->printPacket($rpacket);
        // parse packet
        $this->maskWriteRegisterParser($rpacket);
        // disconnect
        $this->disconnect();
        $this->status .= "maskWriteRegister: DONE" . PHP_EOL;
        return true;
    }

    /**
     * fc22
     *
     * Alias to {@link maskWriteRegister} method
     *
     * @param int $unitId
     * @param int $reference
     * @param int $andMask
     * @param int $orMask
     * @return bool
     * @throws Exception
     */
    public function fc22(int $unitId, int $reference, int $andMask, int $orMask): bool
    {
        return $this->maskWriteRegister($unitId, $reference, $andMask, $orMask);
    }

    /**
     * maskWriteRegisterPacketBuilder
     *
     * Packet builder FC22 - MASK WRITE register
     *
     * @param int $unitId
     * @param int $reference
     * @param int $andMask
     * @param int $orMask
     * @return string
     */
    private function maskWriteRegisterPacketBuilder(int $unitId, int $reference, int $andMask, int $orMask): string
    {
        $dataLen = 0;
        // build data section
        $buffer1 = "";
        // build body
        $buffer2 = IecType::iecBYTE(22);             // FC 22 = 22(0x16)
        $buffer2 .= IecType::iecINT($reference);      // refnumber = 12288
        $buffer2 .= IecType::iecINT($andMask);        // AND mask
        $buffer2 .= IecType::iecINT($orMask);          // OR mask
        $dataLen += 7;
        // build header
        $buffer3 = IecType::iecINT(rand(0, 65000));   // transaction ID
        $buffer3 .= IecType::iecINT(0);               // protocol ID
        $buffer3 .= IecType::iecINT($dataLen + 1);    // lenght
        $buffer3 .= IecType::iecBYTE($unitId);        //unit ID
        // return packet string
        return $buffer3 . $buffer2 . $buffer1;
    }

    /**
     * maskWriteRegisterParser
     *
     * FC22 response parser
     *
     * @param string $packet
     * @return void
     * @throws Exception
     */
    private function maskWriteRegisterParser(string $packet): void
    {
        $this->responseCode($packet);
    }

    /**
     * readWriteRegisters
     *
     * Modbus function FC23(0x17) - Read Write Registers.
     *
     * This function writes {@link $data} array at reference {@link $referenceWrite}
     * position of memory of a Modbus device given by {@link $unitId}. Simultanously,
     * it returns {@link $quantity} of Words (2 bytes) from reference {@link $referenceRead}.
     *
     *
     * @param int $unitId usually ID of Modbus device
     * @param int $referenceRead Reference in the device memory to read data (e.g. in device WAGO 750-841, memory
     *                              MW0 starts at address 12288).
     * @param int $quantity Amounth of the data to be read from device.
     * @param int $referenceWrite Reference in the device memory to write data.
     * @param array $data Array of values to be written.
     * @param array $dataTypes Array of types of values to be written. The array should consists of string "INT",
     *                              "DINT" and "REAL".
     * @return false|array Success flag or array of data.
     * @throws Exception
     */
    public function readWriteRegisters(
        int $unitId,
        int $referenceRead,
        int $quantity,
        int $referenceWrite,
        array $data,
        array $dataTypes
    ) {
        $this->status .= "readWriteRegisters: START" . PHP_EOL;
        // connect
        $this->connect();
        // send FC23
        $packet = $this->readWriteRegistersPacketBuilder(
            $unitId,
            $referenceRead,
            $quantity,
            $referenceWrite,
            $data,
            $dataTypes
        );
        $this->status .= $this->printPacket($packet);
        $this->send($packet);
        // receive response
        $rpacket = $this->rec();
        $this->status .= $this->printPacket($rpacket);
        // parse packet
        $receivedData = $this->readWriteRegistersParser($rpacket);
        // disconnect
        $this->disconnect();
        $this->status .= "writeMultipleRegister: DONE" . PHP_EOL;
        // return
        return $receivedData;
    }

    /**
     * fc23
     *
     * Alias to {@link readWriteRegisters} method.
     *
     * @param int $unitId
     * @param int $referenceRead
     * @param int $quantity
     * @param int $referenceWrite
     * @param array $data
     * @param array $dataTypes
     * @return false|array
     * @throws Exception
     */
    public function fc23(
        int $unitId,
        int $referenceRead,
        int $quantity,
        int $referenceWrite,
        array $data,
        array $dataTypes
    ) {
        return $this->readWriteRegisters($unitId, $referenceRead, $quantity, $referenceWrite, $data, $dataTypes);
    }

    /**
     * readWriteRegistersPacketBuilder
     *
     * Packet FC23 builder - READ WRITE registers
     *
     *
     * @param int $unitId
     * @param int $referenceRead
     * @param int $quantity
     * @param int $referenceWrite
     * @param array $data
     * @param array $dataTypes
     * @return string
     * @throws Exception
     */
    private function readWriteRegistersPacketBuilder(
        int   $unitId,
        int   $referenceRead,
        int   $quantity,
        int   $referenceWrite,
        array $data,
        array $dataTypes
    ): string {
        $dataLen = 0;
        // build data section
        $buffer1 = "";
        foreach ($data as $key => $dataitem) {
            if ($dataTypes[$key] == "INT") {
                $buffer1 .= IecType::iecINT($dataitem);   // register values x
                $dataLen += 2;
            } elseif ($dataTypes[$key] == "DINT") {
                $buffer1 .= IecType::iecDINT($dataitem, $this->endianness);   // register values x
                $dataLen += 4;
            } elseif ($dataTypes[$key] == "REAL") {
                $buffer1 .= IecType::iecREAL($dataitem, $this->endianness);   // register values x
                $dataLen += 4;
            } else {
                $buffer1 .= IecType::iecINT($dataitem);   // register values x
                $dataLen += 2;
            }
        }
        // build body
        $buffer2 = IecType::iecBYTE(23);             // FC 23 = 23(0x17)
        // build body - read section
        $buffer2 .= IecType::iecINT($referenceRead);  // refnumber = 12288
        $buffer2 .= IecType::iecINT($quantity);       // quantity
        // build body - write section
        $buffer2 .= IecType::iecINT($referenceWrite); // refnumber = 12288
        $buffer2 .= IecType::iecINT($dataLen / 2);      // word count
        $buffer2 .= IecType::iecBYTE($dataLen);       // byte count
        $dataLen += 10;
        // build header
        $buffer3 = IecType::iecINT(rand(0, 65000));   // transaction ID
        $buffer3 .= IecType::iecINT(0);               // protocol ID
        $buffer3 .= IecType::iecINT($dataLen + 1);    // lenght
        $buffer3 .= IecType::iecBYTE($unitId);        //unit ID

        // return packet string
        return $buffer3 . $buffer2 . $buffer1;
    }

    /**
     * readWriteRegistersParser
     *
     * FC23 response parser
     *
     * @param string $packet
     * @return array|false
     * @throws Exception
     */
    private function readWriteRegistersParser(string $packet)
    {
        $data = array();
        // if not exception
        if (!$this->responseCode($packet)) {
            return false;
        }
        // get data
        for ($i = 0; $i < ord($packet[8]); $i++) {
            $data[$i] = ord($packet[9 + $i]);
        }
        return $data;
    }

    /**
     * byte2hex
     *
     * Parse data and get it to the Hex form
     *
     * @param int $value
     * @return string
     */
    private function byte2hex(int $value): string
    {
        $h = dechex(($value >> 4) & 0x0F);
        $l = dechex($value & 0x0F);
        return "$h$l";
    }

    /**
     * printPacket
     *
     * Print a packet in the hex form
     *
     * @param string $packet
     * @return string
     */
    private function printPacket(string $packet): string
    {
        $str = "Packet: ";
        for ($i = 0; $i < strlen($packet); $i++) {
            $str .= $this->byte2hex(ord($packet[$i]));
        }
        $str .= PHP_EOL;
        return $str;
    }

    /**
     * Set data receive timeout.
     * Writes property timeout_sec
     *
     * @param float $seconds seconds
     */
    public function setTimeout(float $seconds)
    {
        $this->timeout_sec = $seconds;
    }

    /**
     * Set socket read/write timeout. Null = no change.
     *
     * @param float|null $read_timeout_sec data read timeout (seconds, default 0.3)
     * @param float|null $write_timeout_sec data write timeout (seconds, default 1.0)
     * @internal param float $seconds seconds
     */
    public function setSocketTimeout(?float $read_timeout_sec, ?float $write_timeout_sec)
    {
        // Set read timeout if given
        if ($read_timeout_sec !== null) {
            $this->socket_read_timeout_sec = $read_timeout_sec;
        }

        // Set write timeout if given
        if ($write_timeout_sec !== null) {
            $this->socket_write_timeout_sec = $write_timeout_sec;
        }
    }
}
