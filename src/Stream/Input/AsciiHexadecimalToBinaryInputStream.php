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
     * The following space characters should be ignored:
     * - Blank " ": \x20
     * - Tab "\t": \x09
     * - Carrage Return "\n": \x0D
     * - Line Feed "\r": \x0A
     *
     * @var array List of space characters that should be ignored.
     */
    private $spaces = ["\x20", "\x09", "\x0D", "\x0A"];

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
    public function read($length = 1)
    {
        $bin = '';

        $hex = parent::read($length);

        for ($i = 0; $i < strlen($hex); $i++) {

            if (true === $this->isSpace($hex[$i])) {

                continue;
            }

            $this->buffer[] = $hex[$i];

            $bin .= $this->hex2bin();
        }

        // If the end of stream has been reached, the process ends here,
        // otherwise tries to read further bytes from the subordinate stream,
        // till a hexadecimal pair is found or the end of stream has been
        // reached.
        if ( strlen($hex) > 0 && '' === $bin) {

            $bin = $this->read();
        }

        return $bin;
    }

    /**
     * This method shifts two hexadecimal characters from the internal buffer,
     * and converts them into one binary byte.
     *
     * @return string The binary byte converted from the hexadecimal data.
     */
    private function hex2bin()
    {
        $bin = '';

        if (2 === count($this->buffer)) {

            $bin = chr(hexdec(array_shift($this->buffer).array_shift($this->buffer)));
        }

        return $bin;
    }

    /**
     * This method returns a boolean that indicates  whether the given byte
     * represents a space character.
     *
     * @param string $byte The byte data to be tested.
     */
    private function isSpace($byte)
    {
        return in_array($byte, $this->spaces);
    }
}
