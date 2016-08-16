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

        $this->offset = $this->ref->getProperty('offset');
        $this->offset->setAccessible(true);
    }

    public function tearDown()
    {
        $this->offset = null;
        $this->buffer = null;
        $this->ref = null;
    }

    public function testConstructor()
    {
        $stream = new Input\StringInputStream('hello');
        $this->assertEquals('hello', $this->buffer->getValue($stream));
        $this->assertEquals(0, $this->offset->getValue($stream));
        $this->assertFalse($stream->isClosed());
    }

    /**
     * @dataProvider getDataForTestRead
     */
    public function testRead($expected, $buffer, $length)
    {
        $stream = new Input\StringInputStream($buffer);
        $this->assertEquals($expected, $stream->read($length));
    }

    public function getDataForTestRead()
    {
        return [
            ['', 'hello', -1],
            ['hello', 'hello', 5],
            ['hello', 'hello', 10],
            ['he', 'hello', 2]
        ];
    }

    public function testClose()
    {
        $stream = new Input\StringInputStream('hello');
        $this->assertSame($stream, $stream->close());
        $this->assertTrue($stream->isClosed());
        $this->assertEquals(0, $this->offset->getValue($stream));
        $this->assertNull($this->buffer->getValue($stream));
    }

    /**
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessage Already closed.
     */
    public function testCloseOnClosedStream()
    {
        $stream = new Input\StringInputStream('hello');
        $stream->close();
        $stream->close();
    }

    public function testSkip()
    {
        $stream = new Input\StringInputStream('hello, world!');

        $this->assertEquals(5, $stream->skip(5));

        $this->assertEquals(8, $stream->skip(9));
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
