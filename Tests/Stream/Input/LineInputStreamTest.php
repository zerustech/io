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

        $this->position = $this->ref->getProperty('position');
        $this->position->setAccessible(true);

        $this->input = $this->ref->getMethod('input');
        $this->input->setAccessible(true);
    }

    public function tearDown()
    {
        $this->buffer = null;
        $this->position = null;
        $this->input = null;
        $this->ref = null;
    }

    public function testConstructor()
    {
        $in = new StringInputStream('hello, world');
        $instance = new LineInputStream($in);

        $this->assertEquals('', $this->buffer->getValue($instance));
        $this->assertEquals(0, $this->position->getValue($instance));
    }

    /**
     * @dataProvider getDataForTestInput
     */
    public function testInput($data, $offset, $length, $count, $result, $position, $available)
    {
        $in = new StringInputStream($data);

        $in->skip($offset);

        $stream = new LineInputStream($in);

        $this->assertEquals($count, $this->input->invokeArgs($stream, [&$bytes, $length]));

        $this->assertEquals($result, $bytes);

        $this->assertEquals($position, $stream->getPosition());

        $this->assertEquals($available, $stream->available());
    }

    public function getDataForTestInput()
    {
        return [
            ["0123456789\nABCDEF\nGHIJK\nLMN", 0, 1, 11, "0123456789\n", 11, 16],
            ["0123456789\nABCDEF\nGHIJK\nLMN", 0, 5, 11, "0123456789\n", 11, 16],
            ["0123456789\nABCDEF\nGHIJK\nLMN", 0, 11, 11, "0123456789\n", 11, 16],
            ["0123456789\nABCDEF\nGHIJK\nLMN", 0, 12, 11, "0123456789\n", 11, 16],
            ["0123456789\nABCDEF\nGHIJK\nLMN", 4, 8, 7, "456789\n", 11, 16],
            ["0123456789\nABCDEF\nGHIJK\nLMN", 10, 8, 1, "\n", 11, 16],
            ["0123456789\nABCDEF\nGHIJK\nLMN", 11, 8, 7, "ABCDEF\n", 18, 9],
            ["0123456789\nABCDEF\nGHIJK\nLMN", 24, 8, 3, "LMN", 27, 0],
            ["0123456789\nABCDEF\nGHIJK\nLMN", 25, 8, 2, "MN", 27, 0],
            ["0123456789\nABCDEF\nGHIJK\nLMN", 27, 8, -1, "", 27, 0],
            ["0123456789\nABCDEF\nGHIJK\nLMN", 28, 8, -1, "", 27, 0],
        ];
    }
}
