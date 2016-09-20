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

use ZerusTech\Component\IO\Exception;
use ZerusTech\Component\IO\Stream\Input\StringInputStream;
use ZerusTech\Component\IO\Stream\Input\WashInputStream;

/**
 * Test case for wash input stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class WashInputStreamTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Input\WashInputStream');

        $this->input = $this->ref->getMethod('input');
        $this->input->setAccessible(true);

        $this->searchPattern = $this->ref->getProperty('searchPattern');
        $this->searchPattern->setAccessible(true);

        $this->replacePattern = $this->ref->getProperty('replacePattern');
        $this->replacePattern->setAccessible(true);

        $this->in = $this->ref->getProperty('in');
        $this->in->setAccessible(true);
    }

    public function tearDown()
    {
        $this->in = null;
        $this->input = null;
        $this->searchPattern = null;
        $this->replacePattern = null;
        $this->ref = null;
    }

    /**
     * @dataProvider getDataForTestConstructor
     */
    public function testConstructor($search, $replace)
    {
        $in = new StringInputStream('hello');
        $stream = new WashInputStream($in, $search, $replace);
        $this->assertSame($in, $this->in->getValue($stream));
        $this->assertEquals($search, $this->searchPattern->getValue($stream));
        $this->assertEquals($replace, $this->replacePattern->getValue($stream));
    }

    public function getDataForTestConstructor()
    {
        return [
            ["/([^\n\t ]*)([\n\t ]*)([^\n\t ]*)/", "$1$3"],
            ["/([^ ]*)([ ]*)([^ ]*)/", "$1$3"],
        ];
    }

    /**
     * @dataProvider getDataForTestInput
     */
    public function testInput($source, $offset, $length, $expected, $count, $skipped, $available)
    {
        $in = new StringInputStream($source);
        $stream = new WashInputStream($in);
        $this->assertEquals($skipped, $stream->skip($offset));
        $this->assertEquals($count, $this->input->invokeArgs($stream, [&$bytes, $length]));
        $this->assertEquals($expected, $bytes);
        $this->assertEquals($available, $stream->available());
    }

    public function getDataForTestInput()
    {
        return [
            ["68656C6C6F\n", 0, 10, "68656C6C6F", 10, 0, 1],
            ["68656C6C6F\r", 0, 10, "68656C6C6F", 10, 0, 1],
            ["68656C6C6F\t", 0, 10, "68656C6C6F", 10, 0, 1],
            ["68656C6C6F ", 0, 10, "68656C6C6F", 10, 0, 1],
            ["68656C6C6F\n", 0, 11, "68656C6C6F", 10, 0, 0],
            ["68656C6C6F\r", 0, 11, "68656C6C6F", 10, 0, 0],
            ["68656C6C6F\t", 0, 11, "68656C6C6F", 10, 0, 0],
            ["68656C6C6F ", 0, 11, "68656C6C6F", 10, 0, 0],
            ["6865\n6C6C6F\r", 0, 10, "68656C6C6F", 10, 0, 1],
            ["6865\n6C6C6F\r", 0, 11, "68656C6C6F", 10, 0, 0],
            ["6865\n6C6C6F\r", 0, 12, "68656C6C6F", 10, 0, 0],
            ["6865\t6C6C6F ", 0, 11, "68656C6C6F", 10, 0, 0],
            ["6865 6C6C6F\n", 0, 11, "68656C6C6F", 10, 0, 0],
            ["6865\t6C6C6F\n", 2, 8, "656C6C6F", 8, 2, 1],
            ["6865\t6C6C6F\n", 4, 6, "6C6C6F", 6, 4, 1],
            ["6865\t6C6C6F\n", 5, 5, "C6C6F", 5, 5, 1],
            ["6865\t6C6C6F\n", 6, 4, "6C6F", 4, 6, 1],
            ["6865\t6C6C6F\n", 10, 1, "", -1, 10, 0],
            ["68656C6C6F\n", 0, 2, "68", 2, 0, 1],
            ["6865\n6C6C6F\r", 0, 6, "68656C", 6, 0, 1],
        ];
    }
}
