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
    /**
     * @dataProvider getDataForTestConstructor
     */
    public function testConstructor($closed, $source, $mode)
    {
        $stream = new Output\FileOutputStream($source, $mode);

        $this->assertEquals($source, $stream->getSource());

        $this->assertEquals($mode, $stream->getMode());

        $this->assertEquals($closed, $stream->isClosed());

        $resource = $stream->getResource();

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

    public function testWrite()
    {
        $stream = new Output\FileOutputStream('php://memory', 'rb+');

        $stream->write('hello');

        $resource = $stream->getResource();

        rewind($resource);

        $data = fread($resource, 5);

        $this->assertEquals('hello', $data);
    }

    /**
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessageRegExp /File [^ ]+ is already closed, can't be written./
     */
    public function testWriteOnClosedStream()
    {
        $stream = new Output\FileOutputStream('php://memory', 'wb');
        $stream->close();
        $stream->write('hello');
    }

    /**
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessageRegExp /An unknown error occured when writing to file [^ ]+./
     */
    public function testWriteOnClosedFile()
    {
        $stream = new Output\FileOutputStream('php://memory', 'wb');
        $resource = $stream->getResource();
        fclose($resource);
        $stream->write('hello');
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
        $resource = $stream->getResource();
        fclose($resource);
        $stream->flush();
    }

    public function testClose()
    {
        $stream = new Output\FileOutputStream('php://memory', 'wb');
        $this->assertSame($stream, $stream->close());
        $this->assertTrue($stream->isClosed());
        $this->assertNull($stream->getResource());
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

        $resource = $stream->getResource();

        fclose($resource);

        $stream->close();
    }
}
