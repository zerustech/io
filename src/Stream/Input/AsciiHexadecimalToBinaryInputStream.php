<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For full copyright and license information, please view the LICENSE file that
 * was distributed with this source code.
 */

namespace ZerusTech\Component\IO\Stream\Input;

use ZerusTech\Component\IO\Exception\IOException;
use ZerusTech\Componnet\IO\Stream\Output\BinaryToAsciiHexadecimalOutputStream;

/**
 * This class converts the data read from the subordinate input stream from
 * ascii hexadecimal format to binary format.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 * @see BinaryToAsciiHexadecimalOutputStream
 */
class AsciiHexadecimalToBinaryInputStream extends FilterInputStream
{
    /**
     * Regex pattern for matching a single space character.
     *
     * The following characters are treated as a space character:
     * - Blank " ": \x20
     * - Tab "\t": \x09
     * - Carrage Return "\n": \x0D
     * - Line Feed "\r": \x0A
     *
     * @var string Regex pattern for matching a space character.
     */
    private static $spaces = "/^[ \t\r\n]$/";

    /**
     * Regex pattern for matching at least one non-hexadecimal character.
     *
     * @var string Regex pattern for matching at least one non-hexadecimal
     * character.
     */
    private static $nonHex = "/[^0-9a-fA-F]/";

    /**
     * @var array The internal buffer that stores a pair of hexadecimal
     * characters that are read from the subordinate input stream most recently.
     */
    private $buffer = [];

    /**
     * {@inheritdoc}
     *
     * This method reads up to ``$length`` bytes from the subordinate input
     * stream and converts the data from ascii hexadecimal format to binary
     * format.
     */
    protected function input(&$buffer, $length)
    {
        $buffer = '';

        $count = parent::input($hex, $length);

        for ($i = 0; $i < $count; $i++) {

            if (true === static::isSpace($hex[$i])) {

                continue;
            }

            $this->buffer[] = $hex[$i];

            if (2 === count($this->buffer)) {

                $buffer .= chr(hexdec(array_shift($this->buffer).array_shift($this->buffer)));
            }
        }

        // If the end of stream has been reached, the process ends here,
        // otherwise tries to read further bytes from the subordinate stream,
        // till a hexadecimal pair is found or the end of stream has been
        // reached.
        if ( $count > 0 && '' === $buffer) {

            $count += max(0, $this->input($buffer, 1));
        }

        return $count;
    }

    /**
     * This method returns a boolean that indicates whether the given byte
     * represents a space character.
     *
     * @param string $byte The byte data to be tested.
     * @return bool True if the given byte is a space character, or false
     * otherwise.
     */
    public static function isSpace($byte)
    {
        return (1 === preg_match(static::$spaces, $byte));
    }

    /**
     * This method returns a boolean that indicates whether there is at least
     * one non-hexadecimal character ([^0-9a-fA-F]) in the given string.
     *
     * @param string $bytes The string to be tested.
     * @return bool True if a non-hexadecimal character is found, false
     * otherwise.
     */
    public static function hasNonHexadecimalCharacter($bytes)
    {
        return (1 === preg_match(static::$nonHex, $bytes));
    }
}
