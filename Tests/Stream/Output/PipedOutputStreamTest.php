<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For full copyright and license information, please view the LICENSE file that
 * was distributed with this source code.
 */

namespace ZerusTech\Component\IO\Tests\Stream\Output;

use ZerusTech\Component\IO\Exception\IOException;
use ZerusTech\Component\IO\Stream\Input\PipedInputStream;
use ZerusTech\Component\IO\Stream\Output\PipedOutputStream;

/**
 * Test case for piped output stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class PipedOutputStreamTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Output\PipedOutputStream');

        $this->downstream = $this->ref->getProperty('downstream');
        $this->downstream->setAccessible(true);

        $this->downstreamRef = new \ReflectionClass('ZerusTech\Component\IO\Stream\Input\PipedInputStream');
        $this->buffer = $this->downstreamRef->getProperty('buffer');
        $this->buffer->setAccessible(true);
    }

    public function tearDown()
    {
        $this->downstream = null;
        $this->ref = null;

        $this->buffer = null;
        $this->downstreamRef = null;
    }

    public function testConstructor()
    {
        $downstream = new PipedInputStream();
        $output = new PipedOutputStream($downstream);
        $this->assertSame($downstream, $this->downstream->getValue($output));
        $this->assertFalse($output->isClosed());
    }

    public function testConstructorWithNull()
    {
        $output = new PipedOutputStream();
        $this->assertNull($this->downstream->getValue($output));
        $this->assertFalse($output->isClosed());
    }

    /**
     * Connects piped output stream to a piped input stream.
     */
    public function testConnect()
    {
        $downstream = new PipedInputStream();

        $output = new PipedOutputStream();
        $output->connect($downstream);

        $this->assertSame($downstream, $this->downstream->getValue($output));
        $this->assertFalse($output->isClosed());
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
        $downstream = new PipedInputStream();
        $output = new PipedOutputStream();
        $output->connect($downstream);
        $output->connect($downstream);
    }

    /**
     * Connects a piped output stream to a piped input stream and allows the
     * connect() method of the piped input stream to be called.
     */
    public function testReverseConnect()
    {
        // Initializes a piped output stream.
        $output = new PipedOutputStream();

        // Initializes a piped input stream.
        $downstream = $this->getMockBuilder('ZerusTech\Component\IO\Stream\Input\PipedInputStream')->getMock();
        $downstream->expects($this->once())->method('connect')->with($output);

        // Connects the piped output stream to the piped input stream.
        $output->connect($downstream);
    }

    /**
     * Connects a piped output stream to a piped input stream and disallows the
     * connect() method of the piped input stream to be called.
     */
    public function testNonReverseConnect()
    {
        // Initializes a piped input stream.
        $downstream = $this->getMockBuilder('ZerusTech\Component\IO\Stream\Input\PipedInputStream')->getMock();
        $downstream->expects($this->never())->method('connect');

        // Initializes a piped output stream.
        $output = new PipedOutputStream();

        // Connects the piped output stream to the piped input stream, but sets
        // the 'reverse' argument to false.
        $output->connect($downstream, false, false);
    }

    /**
     * Force a piped output stream to connect to a piped input stream and its
     * connected input stream is overwritten.
     */
    public function testForceConnect()
    {
        $downstream = new PipedInputStream();

        $output = new PipedOutputStream();
        $output->connect($downstream);
        $output->connect($downstream, true);

        $this->assertSame($downstream, $this->downstream->getValue($output));
        $this->assertFalse($output->isClosed());
    }

    public function testFlush()
    {
        $output = new PipedOutputStream();
        $this->assertSame($output, $output->flush());
    }

    /**
     * Tries to close a piped output stream that is already closed.
     *
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessage Stream is already closed, can't be closed again
     */
    public function testCloseOnClosedStream()
    {
        $output = new PipedOutputStream();
        $output->close();
        $output->close();
    }

    /**
     * Writes data to a piped output stream.
     */
    public function testWrite()
    {
        // Initializes a piped input stream.
        $downstream = new PipedInputStream();

        // Initializes a piped output stream.
        $output = new PipedOutputStream($downstream);

        // Writes '*' to the piped output stream.
        $data = '*';

        $output->write($data);

        $this->assertEquals($data, $this->buffer->getValue($downstream));
    }

    /**
     * Writes data to a closed piped output stream.
     *
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessage Stream is already closed, can't be written.
     */
    public function testWriteOnClosedStream()
    {
        $output = new PipedOutputStream();
        $output->close();
        $output->write('hello');
    }

    /**
     * Writes data to a piped output stream, and the downstream of which is
     * null.
     *
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessage Current stream is not connected to any downstream.
     */
    public function testWriteWithNullDownstream()
    {
        $output = new PipedOutputStream();
        $output->write('hello');
    }
}
