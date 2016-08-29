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

use ZerusTech\Component\IO\Exception\IOException;
use ZerusTech\Component\IO\Stream\Input\PipedInputStream;
use ZerusTech\Component\IO\Stream\Output\PipedOutputStream;

/**
 * Test case for piped input stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class PipedInputStreamTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Input\PipedInputStream');

        $this->upstream = $this->ref->getProperty('upstream');
        $this->upstream->setAccessible(true);

        $this->buffer = $this->ref->getProperty('buffer');
        $this->buffer->setAccessible(true);

        $this->input = $this->ref->getMethod('input');
        $this->input->setAccessible(true);
    }

    public function tearDown()
    {
        $this->buffer = null;
        $this->upstream = null;
        $this->ref = null;
    }

    public function testConstructor()
    {
        $upstream = new PipedOutputStream();
        $input = new PipedInputStream($upstream);
        $this->assertSame($upstream, $this->upstream->getValue($input));
        $this->assertFalse($input->isClosed());
        $this->assertEquals('', $this->buffer->getValue($input));
    }

    public function testConstructorWithNullUpstream()
    {
        $input = new PipedInputStream();
        $this->assertNull($this->upstream->getValue($input));
        $this->assertFalse($input->isClosed());
    }

    /**
     * Connects piped output stream to a piped input stream.
     */
    public function testConnect()
    {
        $upstream = new PipedOutputStream();

        $input = new PipedInputStream();
        $input->connect($upstream);

        $this->assertSame($upstream, $this->upstream->getValue($input));
        $this->assertFalse($upstream->isClosed());
    }

    /**
     * Connects a piped output stream, which is already connected, to a piped
     * input stream.
     *
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessage Already connected.
     */
    public function testConnectOnConnectedStream()
    {
        $upstream = new PipedOutputStream();

        $input = new PipedInputStream();
        $input->connect($upstream);
        $input->connect($upstream);
    }

    /**
     * Connects a piped output stream to a piped input stream and allows the
     * connect() method of the piped output stream to be called.
     */
    public function testReverseConnect()
    {
        // Initializes a piped input stream
        $input = new PipedInputStream();

        // Initializeds a piped output stream
        $upstream = $this->getMockBuilder('ZerusTech\Component\IO\Stream\Output\PipedOutputStream')->getMock();
        $upstream->expects($this->once())->method('connect')->with($input);

        // Connects the piped input stream to the piped output stream
        $input->connect($upstream);
    }

    /**
     * Connects a piped output stream to a piped input stream and disallows the
     * connect() method of the piped output stream to be called.
     */
    public function testNonReverseConnect()
    {
        // Initializes a piped output stream
        $upstream = new PipedOutputStream();

        // Initializeds a piped output stream
        $upstream = $this->getMockBuilder('ZerusTech\Component\IO\Stream\Output\PipedOutputStream')->getMock();
        $upstream->expects($this->never())->method('connect');

        // Initializes a piped input stream.
        $input = new PipedInputStream();

        // Connects the piped input stream to the piped output stream.
        // But sets 'reverse' to false.
        $input->connect($upstream, false, false);
    }

    /**
     * Force a piped output stream to connect to a piped input stream and its
     * connected input stream is overwritten.
     */
    public function testForceConnect()
    {
        $upstream = new PipedOutputStream();

        $input = new PipedInputStream();
        $input->connect($upstream);
        $input->connect($upstream, true);

        $this->assertSame($upstream, $this->upstream->getValue($input));
        $this->assertFalse($upstream->isClosed());
    }

    public function testAvailable()
    {
        $input = new PipedInputStream();

        $this->buffer->setValue($input, '**');

        $this->assertEquals(2, $input->available());
    }

    /**
     * @dataProvider getDataForTestInput
     */
    public function testInput($data, $length, $expected, $count, $position, $available)
    {
        $input = new PipedInputStream();

        $this->buffer->setValue($input, $data);

        $this->assertEquals($count, $this->input->invokeArgs($input, [&$bytes, $length]));

        $this->assertEquals($expected, $bytes);

        $this->assertEquals($position, $input->getPosition());

        $this->assertEquals($available, $input->available());
    }

    public function getDataForTestInput()
    {
        return [
            ['***', 1, '*', 1, 1, 2],
            ['***', 3, '***', 3, 3, 0],
            ['***', 5, '***', 3, 3, 0],
            ['', 2, '', -1, 0, 0],
        ];
    }

    public function testReceive()
    {
        $input = new PipedInputStream();

        $this->buffer->setValue($input, 'hello');

        $this->assertEquals(8, $input->receive(', world!'));

        $this->assertEquals('hello, world!', $this->buffer->getValue($input));

        $this->assertEquals(0, $input->getPosition());

        $this->assertEquals(13, $input->available());
    }
}
