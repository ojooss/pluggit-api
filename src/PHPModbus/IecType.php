<?php

namespace PluggitApi\PHPModbus;

use Exception;

/**
 * Phpmodbus Copyright (c) 2004, 2013 Jan Krakora
 *
 * This source file is subject to the "PhpModbus license" that is bundled
 * with this package in the file license.txt.
 *
 * @author    Jan Krakora
 * @copyright Copyright (c) 2004, 2013 Jan Krakora
 * @license   PhpModbus license
 * @category  Phpmodbus
 * @package   Phpmodbus
 * @version   $id$
 */

/**
 * IecType
 *
 * The class includes set of IEC-1131 data type functions that converts a PHP
 * data types to a IEC data type.
 *
 * @author     Jan Krakora
 * @copyright  Copyright (c) 2004, 2010 Jan Krakora
 * @package    Phpmodbus
 */
class IecType
{

    public const LITTLE_ENDIAN = 0;
    public const BIG_ENDIAN = 1;

    /**
     * iecBYTE
     *
     * Converts a value to IEC-1131 BYTE data type
     *
     * @param int $value from 0 to 255
     * @return string IEC BYTE data type
     *
     */
    public static function iecBYTE(int $value): string
    {
        return chr($value & 0xFF);
    }

    /**
     * iecINT
     *
     * Converts a value to IEC-1131 INT data type
     *
     * @param int $value to be converted
     * @return string IEC-1131 INT data type
     *
     */
    public static function iecINT(int $value): string
    {
        return self::iecBYTE(($value >> 8) & 0x00FF) . self::iecBYTE(($value & 0x00FF));
    }

    /**
     * iecDINT
     *
     * Converts a value to IEC-1131 DINT data type
     *
     * @param int $value to be converted
     * @param int $endianness defines endian codding
     * @return string IEC-1131 INT data type
     * @throws Exception
     */
    public static function iecDINT(int $value, int $endianness = self::LITTLE_ENDIAN): string
    {
        // result with right endianness
        return self::endianness($value, $endianness);
    }

    /**
     * iecREAL
     *
     * Converts a value to IEC-1131 REAL data type. The function uses function  @use float2iecReal.
     *
     * @param int $value to be converted
     * @param int $endianness defines endian codding
     * @return string IEC-1131 REAL data type
     * @throws Exception
     */
    public static function iecREAL(int $value, int $endianness = self::LITTLE_ENDIAN): string
    {
        // iecREAL representation
        $real = self::float2iecReal($value);
        // result with right endianness
        return self::endianness($real, $endianness);
    }

    /**
     * float2iecReal
     *
     * This function converts float value to IEC-1131 REAL single precision form.
     *
     * For more see [{@link http://en.wikipedia.org/wiki/Single_precision Single precision on Wiki}] or
     * [{@link http://de.php.net/manual/en/function.base-convert.php PHP base_convert function commentary}, Todd Stokes
     * @ Georgia Tech 21-Nov-2007] or
     * [{@link http://www.php.net/manual/en/function.pack.php PHP pack/unpack functionality}]
     *
     * @param float $value to be converted
     * @return int IEC REAL data type
     */
    private static function float2iecReal(float $value): int
    {
        // get float binary string
        $float = pack("f", $value);
        // set 32-bit unsigned integer of the float
        $w = unpack("L", $float);
        return $w[1];
    }

    /**
     * endianness
     *
     * Make endianess as required.
     * For more see http://en.wikipedia.org/wiki/Endianness
     *
     * @param int $value
     * @param int $endianness
     * @return string
     * @throws Exception
     */
    private static function endianness(int $value, int $endianness = self::LITTLE_ENDIAN): string
    {
        if ($endianness === self::LITTLE_ENDIAN) {
            return
                self::iecBYTE(($value >> 8) & 0x000000FF) .
                self::iecBYTE(($value & 0x000000FF)) .
                self::iecBYTE(($value >> 24) & 0x000000FF) .
                self::iecBYTE(($value >> 16) & 0x000000FF);
        } elseif ($endianness === self::BIG_ENDIAN) {
            return
                self::iecBYTE(($value >> 24) & 0x000000FF) .
                self::iecBYTE(($value >> 16) & 0x000000FF) .
                self::iecBYTE(($value >> 8) & 0x000000FF) .
                self::iecBYTE(($value & 0x000000FF));
        } else {
            throw new Exception('Invalid endianness');
        }
    }
}
