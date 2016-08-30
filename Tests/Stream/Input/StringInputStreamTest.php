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

        $this->position = $this->ref->getProperty('position');
        $this->position->setAccessible(true);

        $this->input = $this->ref->getMethod('input');
        $this->input->setAccessible(true);
    }

    public function tearDown()
    {
        $this->input = null;
        $this->position = null;
        $this->buffer = null;
        $this->ref = null;
    }

    public function testConstructor()
    {
        $stream = new Input\StringInputStream('hello');
        $this->assertEquals('hello', $this->buffer->getValue($stream));
        $this->assertEquals(0, $this->position->getValue($stream));
        $this->assertFalse($stream->isClosed());
    }

    /**
     * @dataProvider getDataForTestInput
     */
    public function testInput($buffer, $length, $count, $result, $available)
    {
        $bytes = '';

        $stream = new Input\StringInputStream($buffer);

        $this->assertEquals($count, $this->input->invokeArgs($stream, [&$bytes, $length]));

        $this->assertEquals($result, $bytes);

        $this->assertEquals($available, $stream->available());
    }

    public function getDataForTestInput()
    {
        return [
            ['hello', 5, 5, 'hello', 0],
            ['hello', 3, 3, 'hel', 2],
            ['', 5, -1, '', 0],
        ];
    }
}
