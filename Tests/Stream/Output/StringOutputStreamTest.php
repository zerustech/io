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
use ZerusTech\Component\IO\Stream\Output\FileOutputStream;
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
    }

    public function tearDown()
    {
        $this->ref = null;

        $this->buffer = null;
    }

    public function testConstructor()
    {
        $stream = new StringOutputStream();

        $this->assertEquals('', $this->buffer->getValue($stream));
    }

    public function testWrite()
    {
        $stream = new StringOutputStream();

        $stream->write('hello');

        $this->assertEquals('hello', $this->buffer->getValue($stream));
    }

    public function testWriteTo()
    {
        $stream = new StringOutputStream();

        $stream->write('hello');

        $out = new FileOutputStream('php://memory', 'rw+');

        $stream->writeTo($out);

        $ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Output\FileOutputStream');
        $resource = $ref->getProperty('resource');
        $resource->setAccessible(true);
        $fp = $resource->getValue($out);
        rewind($fp);

        $this->assertEquals('hello', fread($fp, 5));
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
