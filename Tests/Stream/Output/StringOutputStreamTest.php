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

use ZerusTech\Component\IO\Stream\Output\StringOutputStream;
use ZerusTech\Component\IO\Stream\Output\PipedOutputStream;
use ZerusTech\Component\IO\Stream\Input\PipedInputStream;
use ZerusTech\Component\IO\Exception;

/**
 * Test case for string output stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class StringOutputStreamTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Output\StringOutputStream');

        $this->buffer = $this->ref->getProperty('buffer');
        $this->buffer->setAccessible(true);

        $this->output = $this->ref->getMethod('output');
        $this->output->setAccessible(true);
    }

    public function tearDown()
    {
        $this->ref = null;

        $this->buffer = null;
    }

    public function testConstructor()
    {
        $stream = new StringOutputStream('hello');

        $this->assertEquals('hello', $this->buffer->getValue($stream));
    }

    public function testOutput()
    {
        $stream = new StringOutputStream('hello');

        $this->assertEquals(8, $this->output->invoke($stream, ', world!'));

        $this->assertEquals('hello, world!', $this->buffer->getValue($stream));
    }

    public function testWriteTo()
    {
        $stream = new StringOutputStream();
        $stream->write('hello');

        $out = new PipedOutputStream();
        $in = new PipedInputStream($out);
        $stream->writeTo($out);

        $this->assertEquals(5, $in->read($buffer, 5));
        $this->assertEquals('hello', $buffer);
    }

    public function testMiscMethods()
    {
        $stream = new StringOutputStream();

        $stream->write('hello');

        $this->assertEquals(5, $stream->size());

        $this->assertEquals('hello', $stream->__toString());

        $this->assertEquals(0, $stream->reset()->size());
    }
}
