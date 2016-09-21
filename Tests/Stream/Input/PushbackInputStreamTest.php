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

use ZerusTech\Component\IO\Stream\Input\PushbackInputStream;
use ZerusTech\Component\IO\Stream\Input\StringInputStream;
use ZerusTech\Component\IO\Exception;

/**
 * Test case for pushback input stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class PushbackInputStreamTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Input\PushbackInputStream');

        $this->in = $this->ref->getProperty('in');
        $this->in->setAccessible(true);

        $this->buffer = $this->ref->getProperty('buffer');
        $this->buffer->setAccessible(true);

        $this->bufferSize = $this->ref->getProperty('bufferSize');
        $this->bufferSize->setAccessible(true);

        $this->readBufferSize = $this->ref->getProperty('readBufferSize');
        $this->readBufferSize->setAccessible(true);

        $this->pushback = $this->ref->getMethod('pushback');
        $this->pushback->setAccessible(true);
    }

    public function tearDown()
    {
        $this->pushback = null;
        $this->bufferSize = null;
        $this->in = null;
        $this->ref = null;
    }

    /**
     * @dataProvider getDataForTestConstructor
     */
    public function testConstructor($bufferSize, $readBufferSize, $expectedBufferSize, $expectedReadBufferSize)
    {
        $in = new StringInputStream('hello');

        if (null !== $bufferSize && null !== $readBufferSize) {

            $instance = new PushbackInputStream($in, $bufferSize, $readBufferSize);

        } else {

            $instance = new PushbackInputStream($in);
        }

        $this->assertSame($in, $this->in->getValue($instance));

        $this->assertEquals($expectedBufferSize, $this->bufferSize->getValue($instance));

        $this->assertEquals($expectedReadBufferSize, $this->readBufferSize->getValue($instance));
    }

    public function getDataForTestConstructor()
    {
        return [
            [32, 16, 32, 16],
            [128, 32, 128, 32],
            [null, null, 1, 1024],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegex /The buffer size must be greater than \d+./
     */
    public function testConstructorWithException()
    {
        $instance = new PushbackInputStream(new StringInputStream('hello'), 0);
    }

    public function testUnread()
    {
        $stub = $this
            ->getMockBuilder('ZerusTech\Component\IO\Stream\Input\PushbackInputStream')
            ->setConstructorArgs([new StringInputStream('world'), 16, 32])
            ->setMethods(['unreadSubstring'])
            ->getMock();

        $stub->expects($this->once())->method('unreadSubstring')->with('hello', 0, 5)->willReturn(null);

        $stub->unread('hello');
    }

    /**
     * @dataProvider getDataForTestUnreadSubstring
     */
    public function testUnreadSubstring($bufferSize, $readBufferSize, $source, $offset, $length, $expected)
    {
        $stub = $this
            ->getMockBuilder('ZerusTech\Component\IO\Stream\Input\PushbackInputStream')
            ->setConstructorArgs([new StringInputStream('world'), $bufferSize, $readBufferSize])
            ->setMethods(['pushback'])
            ->getMock();

        $stub->expects($this->once())->method('pushback')->with($expected)->willReturn(null);

        $stub->unreadSubstring($source, $offset, $length);
    }

    public function getDataForTestUnreadSubstring()
    {
        return [
            [16, 32, 'hello', 0, 5, 'hello'],
            [16, 32, 'hello', -1, 5, 'o'],
            [16, 32, 'hello', -5, 5, 'hello'],
            [16, 32, 'hello', -6, 5, 'hello'],
            [16, 32, 'hello', 0, -1, 'hell'],
            [16, 32, 'hello', 2, -1, 'll'],
            [16, 32, 'hello', 2, -3, ''],
        ];
    }

    public function testPushback()
    {
        $in = new StringInputStream('world');

        $instance = new PushbackInputStream($in, 16);

        $this->pushback->invoke($instance, 'hello');

        $this->assertEquals('hello', $this->buffer->getValue($instance));

        $this->assertEquals(10, $instance->available());
    }

    /**
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMsg Insufficient space in pushback buffer
     */
    public function testPushbackWithException()
    {
        $instance = new PushbackInputStream(new StringInputStream('world'), 2);

        $this->pushback->invoke($instance, 'hello');
    }
}
