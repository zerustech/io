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

use ZerusTech\Component\IO\Stream\Input\BufferableFilterInputStream;
use ZerusTech\Component\IO\Stream\Input\StringInputStream;
use ZerusTech\Component\IO\Exception;

/**
 * Test case for bufferable filter input stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class BufferableFilterInputStreamTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Input\BufferableFilterInputStream');

        $this->in = $this->ref->getProperty('in');
        $this->in->setAccessible(true);

        $this->readBufferSize = $this->ref->getProperty('readBufferSize');
        $this->readBufferSize->setAccessible(true);

        $this->buffer = $this->ref->getProperty('buffer');
        $this->buffer->setAccessible(true);

        $this->input = $this->ref->getMethod('input');
        $this->input->setAccessible(true);
    }

    public function tearDown()
    {
        $this->buffer = null;
        $this->readBufferSize = null;
        $this->in = null;
        $this->ref = null;
    }

    /**
     * @dataProvider getDataForTestConstructor
     */
    public function testConstructor($readBufferSize, $expected)
    {
        $in = new StringInputStream('hello');

        if (null !== $readBufferSize) {

            $instance = new BufferableFilterInputStream($in, $readBufferSize);

        } else {

            $instance = new BufferableFilterInputStream($in);

        }

        $this->assertSame($in, $this->in->getValue($instance));
        $this->assertEquals($expected, $this->readBufferSize->getValue($instance));
    }

    public function getDataForTestConstructor()
    {
        return [
            [32, 32],
            [64, 64],
            [128, 128],
            [null, 1024],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegex /The read buffer size must be greater than \d+./
     */
    public function testConstructorWithException()
    {
        $instance = new BufferableFilterInputStream(new StringInputStream('hello'), 0);
    }

    /**
     * @dataProvider getDataForTestInput
     */
    public function testInput($source, $readBufferSize, $buffer, $offset, $length, $count, $skipped, $available)
    {
        $in = new StringInputStream($source);

        $stream = new BufferableFilterInputStream($in, $readBufferSize);

        $this->buffer->setValue($stream, $buffer);

        $this->assertEquals($skipped, $stream->skip($offset));

        $this->assertEquals($count, $this->input->invokeArgs($stream, [&$bytes, $length]));

        $this->assertEquals($available, $stream->available());
    }

    public function getDataForTestInput()
    {
        return [
            ['world', 2, 'hello', 0, 3, 3, 0, 7],
            ['world', 2, 'hello', 0, 5, 5, 0, 5],
            ['world', 2, 'hello', 0, 6, 6, 0, 4],
            ['world', 2, 'hello', 0, 9, 9, 0, 1],
            ['world', 2, 'hello', 0, 10, 10, 0, 0],
            ['world', 2, 'hello', 0, 11, 10, 0, 0],
            ['world', 2, 'hello', 3, 2, 2, 3, 5],
            ['world', 2, 'hello', 5, 2, 2, 5, 3],
            ['world', 2, 'hello', 10, 2, -1, 10, 0],
        ];
    }
}
