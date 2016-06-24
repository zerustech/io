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
 * Test case for file input stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class FileInputStreamTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->base = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR;
    }

    public function tearDown()
    {
        $this->base = null;
    }

    /**
     * @dataProvider getDataForTestConstructor
     */
    public function testConstructor($closed, $source, $mode)
    {
        $source = $this->base.$source;

        $stream = new Input\FileInputStream($source, $mode);

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
            [false, 'input_01.txt', 'rb'],
            [true, 'no_file.txt', 'rb']
        ];
    }

    public function testRead()
    {
        $stream = new Input\FileInputStream($this->base.'input_01.txt', 'rb');

        $this->assertEquals('hello', $stream->read(5));
    }

    /**
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessageRegExp /An unknown error occured when reading data from file .+/
     */
    public function testIOExceptionForRead()
    {
        $stream = new Input\FileInputStream($this->base.'input_01.txt', 'rb');

        $resource = $stream->getResource();

        fclose($resource);

        $data = $stream->read(5);
    }

    public function testClose()
    {
        $stream = new Input\FileInputStream($this->base.'input_01.txt', 'rb');

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
        $stream = new Input\FileInputStream($this->base.'input_01.txt', 'rb');

        $stream->close();

        $stream->close();
    }

    /**
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessageRegExp /Failed to close .+/
     */
    public function testCloseOnClosedFile()
    {
        $stream = new Input\FileInputStream($this->base.'input_01.txt', 'rb');

        $resource = $stream->getResource();

        fclose($resource);

        $stream->close();
    }
}
