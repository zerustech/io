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
        $this->base = __DIR__.'/../../Fixtures/';

        $this->ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Input\FileInputStream');

        $this->resourceProperty = $this->ref->getProperty('resource');

        $this->resourceProperty->setAccessible(true);
    }

    public function tearDown()
    {
        $this->base = null;

        $this->resourceProperty = null;

        $this->ref = null;
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

        $resource = $this->resourceProperty->getValue($stream);

        if ($resource) {

            $meta = stream_get_meta_data($resource);

            $this->assertEquals($source, $meta['uri']);

            $this->assertEquals($mode, $meta['mode']);
        }

        $ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Input\FileInputStream');
        $property = $ref->getProperty('offset');
        $property->setAccessible(true);
        $this->assertEquals(0, $property->getValue($stream));
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

        $this->assertEquals(", world!\n", $stream->read(10));
    }

    /**
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessageRegExp /An unknown error occured when reading data from file .+/
     */
    public function testIOExceptionForRead()
    {
        $stream = new Input\FileInputStream($this->base.'input_01.txt', 'rb');

        $resource = $this->resourceProperty->getValue($stream);

        fclose($resource);

        $data = $stream->read(5);
    }

    public function testClose()
    {
        $stream = new Input\FileInputStream($this->base.'input_01.txt', 'rb');

        $this->assertSame($stream, $stream->close());

        $this->assertTrue($stream->isClosed());

        $this->assertNull($this->resourceProperty->getValue($stream));

        $ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Input\FileInputStream');
        $property = $ref->getProperty('offset');
        $property->setAccessible(true);
        $this->assertEquals(0, $property->getValue($stream));
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

        $resource = $this->resourceProperty->getValue($stream);

        fclose($resource);

        $stream->close();
    }

    public function testSkip()
    {
        $stream = new Input\FileInputStream($this->base.'input_01.txt', 'rb');

        $this->assertEquals(5, $stream->skip(5));

        $this->assertEquals(9, $stream->skip(10));
    }

    public function testAvaialble()
    {
        $stream = new Input\FileInputStream($this->base.'input_01.txt', 'rb');

        $this->assertEquals(14, $stream->available());

        $stream->skip(4);

        $this->assertEquals(10, $stream->available());

        $stream->skip(10);

        $this->assertEquals(0, $stream->available());

        $stream->skip(1);

        $this->assertEquals(0, $stream->available());
    }
}
