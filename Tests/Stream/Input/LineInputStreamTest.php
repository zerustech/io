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

use ZerusTech\Component\IO\Stream\Input\LineInputStream;
use ZerusTech\Component\IO\Stream\Input\StringInputStream;

/**
 * Test case for line input stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class LineInputStreamTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Input\LineInputStream');

        $this->buffer = $this->ref->getProperty('buffer');
        $this->buffer->setAccessible(true);

        $this->bufferSize = $this->ref->getProperty('bufferSize');
        $this->bufferSize->setAccessible(true);
    }

    public function tearDown()
    {
        $this->buffer = null;
        $this->bufferSize = null;
        $this->input = null;
        $this->ref = null;
    }

    public function testConstructor()
    {
        $in = new StringInputStream('hello, world');
        $instance = new LineInputStream($in);

        $this->assertEquals('', $this->buffer->getValue($instance));
        $this->assertEquals(32, $this->bufferSize->getValue($instance));
    }

    /**
     * @dataProvider getDataForTestReadLine
     */
    public function testReadLine($data, $offset, $expected, $available)
    {
        $in = new StringInputStream($data);

        $in->skip($offset);

        $stream = new LineInputStream($in);

        $this->assertEquals($expected, $stream->readLine());

        $this->assertEquals($available, $stream->available());
    }

    public function getDataForTestReadLine()
    {
        return [
            ["abc\ndef\nhij", 4, "def\n", 3],
            ["0123456789\nABCDEF\nGHIJK\nLMN", 0, "0123456789\n", 16],
            ["0123456789\nABCDEF\nGHIJK\nLMN", 0, "0123456789\n", 16],
            ["0123456789\nABCDEF\nGHIJK\nLMN", 0, "0123456789\n", 16],
            ["0123456789\nABCDEF\nGHIJK\nLMN", 0, "0123456789\n", 16],
            ["0123456789\nABCDEF\nGHIJK\nLMN", 4, "456789\n", 16],
            ["0123456789\nABCDEF\nGHIJK\nLMN", 10, "\n", 16],
            ["0123456789\nABCDEF\nGHIJK\nLMN", 11, "ABCDEF\n", 9],
            ["0123456789\nABCDEF\nGHIJK\nLMN", 24, "LMN", 0],
            ["0123456789\nABCDEF\nGHIJK\nLMN", 25, "MN", 0],
            ["0123456789\nABCDEF\nGHIJK\nLMN", 27, null, 0],
            ["0123456789\nABCDEF\nGHIJK\nLMN", 28, null, 0],
        ];
    }
}
