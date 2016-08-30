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

use ZerusTech\Component\IO\Stream\Input;
use ZerusTech\Component\IO\Exception;

/**
 * Test case for string input stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class StringInputStreamTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Input\StringInputStream');

        $this->buffer = $this->ref->getProperty('buffer');
        $this->buffer->setAccessible(true);

        $this->input = $this->ref->getMethod('input');
        $this->input->setAccessible(true);
    }

    public function tearDown()
    {
        $this->input = null;
        $this->buffer = null;
        $this->ref = null;
    }

    public function testConstructor()
    {
        $stream = new Input\StringInputStream('hello');
        $this->assertEquals('hello', $this->buffer->getValue($stream));
        $this->assertFalse($stream->isClosed());
    }

    /**
     * @dataProvider getDataForTestInput
     */
    public function testInput($buffer, $length, $count, $result, $available)
    {
        $bytes = '';

        $stream = new Input\StringInputStream($buffer);

        $this->assertEquals($count, $this->input->invokeArgs($stream, [&$bytes, $length]));

        $this->assertEquals($result, $bytes);

        $this->assertEquals($available, $stream->available());
    }

    public function getDataForTestInput()
    {
        return [
            ['hello', 5, 5, 'hello', 0],
            ['hello', 3, 3, 'hel', 2],
            ['', 5, -1, '', 0],
        ];
    }

    public function testClose()
    {
        $stream = new Input\StringInputStream('hello');
        $stream->skip(5);

        $this->assertSame($stream, $stream->close());
        $this->assertTrue($stream->isClosed());
        $this->assertNull($this->buffer->getValue($stream));
    }

    /**
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessage Stream is already closed, can't be closed again.
     */
    public function testCloseOnClosedStream()
    {
        $stream = new Input\StringInputStream('hello');
        $stream->close();
        $stream->close();
    }

    public function testAvailable()
    {
        $stream = new Input\StringInputStream('hello, world!');

        $this->assertEquals(13, $stream->available());

        $stream->skip(3);
        $this->assertEquals(10, $stream->available());

        $stream->skip(10);
        $this->assertEquals(0, $stream->available());

        $stream->skip(1);
        $this->assertEquals(0, $stream->available());
    }
}
