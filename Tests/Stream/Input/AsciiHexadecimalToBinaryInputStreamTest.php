<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For full copyright and license information, please view the LICENSE file that
 * was distributed with this source code.
 */

namespace ZerusTech\Component\IO\Tests\Stream\Input;

use ZerusTech\Component\IO\Stream\Input\AsciiHexadecimalToBinaryInputStream;
use ZerusTech\Component\IO\Stream\Input\StringInputStream;
use ZerusTech\Component\IO\Exception;

/**
 * Test case for file ascii hexadecimal to binary input stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class AsciiHexadecimalToBinaryInputStreamTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Input\AsciiHexadecimalToBinaryInputStream');

        $this->input = $this->ref->getMethod('input');
        $this->input->setAccessible(true);
    }

    /**
     * @dataProvider getDataForTestIsSpace
     */
    public function testIsSpace($byte)
    {
        $this->assertTrue(AsciiHexadecimalToBinaryInputStream::isSpace($byte));
    }

    public function getDataForTestIsSpace()
    {
        return [
            [" "],
            ["\t"],
            ["\n"],
            ["\r"],
        ];
    }

    /**
     * @dataProvider getDataForTestInput
     */
    public function testInput($hex, $size, $bin, $position)
    {
        $in = new StringInputStream($hex);

        $stream = new AsciiHexadecimalToBinaryInputStream($in);

        $data = '';

        while (-1 !== ($count = $this->input->invokeArgs($stream, [&$bytes, $size]))) {

            $data .= $bytes;
        }

        $this->assertEquals($bin, $data);

        $this->assertEquals($position, $stream->getPosition());
    }

    public function getDataForTestInput()
    {
        return [
            ['68656C6C6F', 1, 'hello', 10],
            ['68656C6C6F', 2, 'hello', 10],
            ['68656C6C6F', 3, 'hello', 10],
            ['68656C6C6F', 10, 'hello', 10],
            ['68656C6C6F', 20, 'hello', 10],
            ["\n\t\r 68 \n\t\r65\r \n\t6C\t\r \n6C\n\t\r 6F \n\t\r", 1, 'hello', 34],
            ["\n\t\r 68 \n\t\r65\r \n\t6C\t\r \n6C\n\t\r 6F \n\t\r", 2, 'hello', 34],
            ["\n\t\r 68 \n\t\r65\r \n\t6C\t\r \n6C\n\t\r 6F \n\t\r", 3, 'hello', 34],
            ["\n\t\r 68 \n\t\r65\r \n\t6C\t\r \n6C\n\t\r 6F \n\t\r", 10, 'hello', 34],
            ["\n\t\r 68 \n\t\r65\r \n\t6C\t\r \n6C\n\t\r 6F \n\t\r", 100, 'hello', 34],
        ];
    }

    /**
     * @dataProvider getDataForTestHasNonHex
     */
    public function testHasNonHex($bytes, $expected)
    {
        $this->assertEquals($expected, AsciiHexadecimalToBinaryInputStream::hasNonHexadecimalCharacter($bytes));
    }

    public function getDataForTestHasNonHex()
    {
        return [
            ["0123456789abcdefABCDEF", false],
            ["0123456789abcdefABCDEF ", true],
            ["GHIJK", true],
            ["X0123456789abcdefABCDEF", true],
            ["0123456789XabcdefABCDEF", true],
            ["0123456789abcdefABCDEFX", true],
        ];
    }
}
