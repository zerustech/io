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
 * Test case for abstract input stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class AbstractInputStreamTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Input\AbstractInputStream');

        $this->closed = $this->ref->getProperty('closed');
        $this->closed->setAccessible(true);
    }

    public function tearDown()
    {
        $this->closed = null;

        $this->ref = null;
    }

    public function testConstructor()
    {
        $stub = $this->getMockForAbstractClass('ZerusTech\Component\IO\Stream\Input\AbstractInputStream');

        $this->assertFalse($this->closed->getValue($stub));
    }

    public function testRead()
    {
        $stub = $this
            ->getMockBuilder('ZerusTech\Component\IO\Stream\Input\AbstractInputStream')
            ->disableOriginalClone()
            ->setMethods(['input'])
            ->getMock();

        $stub->method('input')->will($this->returnCallback(function(&$bytes, $length){$bytes = 'hello'; return 5;}));

        $this->assertEquals(5, $stub->read($bytes, 5));
        $this->assertEquals('hello', $bytes);
    }

    /**
     * @dataProvider getDataForTestReadSubstring
     */
    public function testReadSubstring($bytes, $offset, $length, $data, $count, $expected)
    {
        $stub = $this
            ->getMockBuilder('ZerusTech\Component\IO\Stream\Input\AbstractInputStream')
            ->disableOriginalClone()
            ->setMethods(['input'])
            ->getMock();

        $stub->method('input')->will($this->returnCallback(function(&$bytes, $length) use($data) {$bytes = substr($data, 0, $length); return $length > 0 && 0 === strlen($bytes) ? -1 : strlen($bytes); }));

        $this->assertEquals($count, $stub->readSubstring($bytes, $offset, $length));

        $this->assertEquals($expected, $bytes);
    }

    public function getDataForTestReadSubstring()
    {
        return [
            ['*****', 0, 5, 'hello', 5, 'hello'],
            ['*****', 1, 5, 'hello', 5, '*hello'],
            ['*****', 5, 5, 'hello', 5, '*****hello'],
            ['*****', -1, 5, 'hello', 5, '****hello'],
            ['*****', -5, 5, 'hello', 5, 'hello'],
            ['*****', -6, 5, 'hello', 5, 'hello'],
            ['', 0, 5, 'hello', 5, 'hello'],
            ['', -1, 5, 'hello', 5, 'hello'],
            ['*****', 0, 1, 'hello', 1, 'h'],
            ['*****', 0, 6, 'hello', 5, 'hello'],
            ['*****', 0, -1, 'hello', 4, 'hell'],
            ['*****', 0, -4, 'hello', 1, 'h'],
            ['*****', 1, 1, 'hello', 1, '*h'],
            ['*****', 1, 6, 'hello', 5, '*hello'],
            ['*****', 1, -1, 'hello', 3, '*hel'],
            ['*****', 1, -3, 'hello', 1, '*h'],
            ['*****', 0, 1, '', -1, ''],
            ['*****', 0, 2, '', -1, ''],
        ];
    }

    /**
     * @dataProvider getDataForTestReadSubstringException
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Invalid offset or length.
     */
    public function testReadSubstringException($bytes, $offset, $length)
    {
        $stub = $this
            ->getMockBuilder('ZerusTech\Component\IO\Stream\Input\AbstractInputStream')
            ->disableOriginalClone()
            ->setMethods(['input'])
            ->getMock();

        $stub->readSubstring($bytes, $offset, $length);
    }

    public function getDataForTestReadSubstringException()
    {
        return [
            ['*****', 6, 5],
            ['', 1, 5],
            ['*****', 0, 0],
            ['*****', 0, null],
            ['*****', 0, false],
            ['*****', 0, -5],
            ['*****', 0, -6],
            ['', 0, -1],
        ];
    }

    /**
     * @dataProvider getDataForTestSkip
     */
    public function testSkip($data, $length, $buffer, $count)
    {
        $stub = $this
            ->getMockBuilder('ZerusTech\Component\IO\Stream\Input\AbstractInputStream')
            ->disableOriginalClone()
            ->setMethods(['input'])
            ->getMock();

        $stub->method('input')->will($this->returnCallback(function(&$bytes, $length) use(&$data) {$bytes = substr($data, 0, $length); $data = substr($data, $length); return 0 === strlen($bytes) ? -1 : strlen($bytes); }));

        $this->assertEquals($count, $stub->skip($length, $buffer));
    }

    public function getDataForTestSkip()
    {
        return [
            ['0123456789ABCDEF', -1, 1024, 0],
            ['0123456789ABCDEF', 0, 1024, 0],
            ['0123456789ABCDEF', 1, 1024, 1],
            ['0123456789ABCDEF', 5, 1024, 5],
            ['0123456789ABCDEF', 16, 1024, 16],
            ['0123456789ABCDEF', 17, 1024, 16],
            ['', 1, 1024, 0],
            [null, 1, 1024, 0],
            [false, 1, 1024, 0],
            ['0123456789ABCDEF', -1, 2, 0],
            ['0123456789ABCDEF', 0, 2, 0],
            ['0123456789ABCDEF', 1, 2, 1],
            ['0123456789ABCDEF', 5, 2, 5],
            ['0123456789ABCDEF', 16, 2, 16],
            ['0123456789ABCDEF', 17, 2, 16],
            ['', 1, 2, 0],
            [null, 1, 2, 0],
            [false, 1, 2, 0],
        ];
    }

    /**
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessage mark/reset not supported.
     */
    public function testMiscMethods()
    {
        $stub = $this->getMockForAbstractClass('ZerusTech\Component\IO\Stream\Input\AbstractInputStream', []);

        $this->assertEquals(0, $stub->available());
        $this->assertSame($stub, $stub->mark(100));
        $this->assertFalse($stub->markSupported());
        $this->assertFalse($stub->isClosed());
        $stub->close();
        $this->assertTrue($stub->isClosed());
        $stub->reset();
    }

    /**
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessage Stream is already closed, can't be closed again.
     */
    public function testCloseException()
    {
        $stub = $this->getMockForAbstractClass('ZerusTech\Component\IO\Stream\Input\AbstractInputStream', []);
        $stub->close();
        $stub->close();
    }
}
