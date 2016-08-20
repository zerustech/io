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

use ZerusTech\Component\IO\Stream\Output;
use ZerusTech\Component\IO\Exception;

/**
 * Test case for file output stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class FileOutputStreamTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Output\FileOutputStream');

        $this->resourceProperty = $this->ref->getProperty('resource');
        $this->resourceProperty->setAccessible(true);

        $this->output = $this->ref->getMethod('output');
        $this->output->setAccessible(true);
    }

    public function tearDown()
    {
        $this->ref = null;

        $this->resource = null;

        $this->output = null;
    }

    /**
     * @dataProvider getDataForTestConstructor
     */
    public function testConstructor($closed, $source, $mode)
    {
        $stream = new Output\FileOutputStream($source, $mode);

        $this->assertEquals($source, $stream->getSource());

        $this->assertEquals($mode, $stream->getMode());

        $this->assertEquals($closed, $stream->isClosed());

        $resource = $this->resourceProperty->getValue($stream);

        if ($resource) {

            $meta = stream_get_meta_data($resource);

            $this->assertEquals($source, $meta['uri']);

            $this->assertEquals($mode, $meta['mode']);
        }
    }

    public function getDataForTestConstructor()
    {
        return [
            [false, 'php://memory', 'w+b'],
            [true, 'php://nofile', 'w+b']
        ];
    }

    public function testOutput()
    {
        $stream = new Output\FileOutputStream('php://memory', 'rb+');

        $resource = $this->resourceProperty->getValue($stream);

        $this->assertEquals(5, $this->output->invoke($stream, 'hello'));

        rewind($resource);

        $data = fread($resource, 5);

        $this->assertEquals('hello', $data);
    }

    /**
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessageRegExp /An unknown error occured when writing to file [^ ]+./
     */
    public function testOutputOnClosedFile()
    {
        $stream = new Output\FileOutputStream('php://memory', 'wb');
        $resource = $this->resourceProperty->getValue($stream);
        fclose($resource);
        $this->output->invoke($stream, 'hello');
    }

    public function testFlush()
    {
        $stream = new Output\FileOutputStream('php://memory', 'wb');
        $this->assertSame($stream, $stream->flush());
    }

    /**
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessageRegExp /File [^ ]+ is already closed, can't be flushed./
     */
    public function testFlushOnClosedStream()
    {
        $stream = new Output\FileOutputStream('php://memory', 'wb');
        $stream->close();
        $stream->flush();
    }

    /**
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessageRegExp /An unknown error occured when flushing file [^ ]+./
     */
    public function testFlushOnClosedFile()
    {
        $stream = new Output\FileOutputStream('php://memory', 'wb');
        $resource = $this->resourceProperty->getValue($stream);
        fclose($resource);
        $stream->flush();
    }

    public function testClose()
    {
        $stream = new Output\FileOutputStream('php://memory', 'wb');
        $this->assertSame($stream, $stream->close());
        $this->assertTrue($stream->isClosed());
        $this->assertNull($this->resourceProperty->getValue($stream));
    }

    /**
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessageRegExp /File [^ ]+ is already closed, can't be closed again./
     */
    public function testCloseOnClosedStream()
    {
        $stream = new Output\FileOutputStream('php://memory', 'wb');

        $stream->close();

        $stream->close();
    }

    /**
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessageRegExp /An unknown error occured when flushing file [^ ]+./
     */
    public function testCloseOnClosedFile()
    {
        $stream = new Output\FileOutputStream('php://memory', 'wb');

        $resource = $this->resourceProperty->getValue($stream);

        fclose($resource);

        $stream->close();
    }
}
