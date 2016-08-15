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
     * @dataProvider getDataForTestRead
     */
    public function testRead($hex, $size, $bin)
    {
        $in = new StringInputStream($hex);

        $stream = new AsciiHexadecimalToBinaryInputStream($in);

        $data = '';

        while ('' !== ($buffer = $stream->read($size))) {

            $data .= $buffer;
        }

        $this->assertEquals($bin, $data);
    }

    public function getDataForTestRead()
    {
        return [
            ['68656C6C6F', 1, 'hello'],
            ['68656C6C6F', 2, 'hello'],
            ['68656C6C6F', 3, 'hello'],
            ['68656C6C6F', 10, 'hello'],
            ['68656C6C6F', 20, 'hello'],
            ["\n\t\r 68 \n\t\r65\r \n\t6C\t\r \n6C\n\t\r 6F \n\t\r", 1, 'hello'],
            ["\n\t\r 68 \n\t\r65\r \n\t6C\t\r \n6C\n\t\r 6F \n\t\r", 2, 'hello'],
            ["\n\t\r 68 \n\t\r65\r \n\t6C\t\r \n6C\n\t\r 6F \n\t\r", 3, 'hello'],
            ["\n\t\r 68 \n\t\r65\r \n\t6C\t\r \n6C\n\t\r 6F \n\t\r", 10, 'hello'],
            ["\n\t\r 68 \n\t\r65\r \n\t6C\t\r \n6C\n\t\r 6F \n\t\r", 100, 'hello'],
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
