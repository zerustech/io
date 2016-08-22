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

        $this->assertEquals(0, $stub->getPosition());
    }

    /**
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessage mark/reset not supported.
     */
    public function testDummyMethods()
    {
        $stub = $this->getMockForAbstractClass('ZerusTech\Component\IO\Stream\Input\AbstractInputStream', []);

        $this->assertEquals(0, $stub->available());
        $this->assertSame($stub, $stub->mark(100));
        $this->assertFalse($stub->markSupported());

        $bytes = '';
        $stub
            ->method('input')
            ->with($bytes, 5)
            ->willReturn(5);

       $this->assertEquals(5, $stub->skip(5));
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
    public function testReadSubstring($source, $offset, $length, $data, $count, $result)
    {
        $stub = $this
            ->getMockBuilder('ZerusTech\Component\IO\Stream\Input\AbstractInputStream')
            ->disableOriginalClone()
            ->setMethods(['input'])
            ->getMock();

        $stub->method('input')->will($this->returnCallback(function(&$bytes, $length) use($data) {$bytes = substr($data, 0, $length); return $length > 0 && 0 === strlen($bytes) ? -1 : strlen($bytes); }));

        $this->assertEquals($count, $stub->readSubstring($source, $offset, $length));

        $this->assertEquals($result, $source);
    }

    public function getDataForTestReadSubstring()
    {
        return [
            ['*****', 0, 5, 'hello', 5, 'hello'],
            ['*****', 1, 5, 'hello', 5, '*hello'],
            ['*****', 3, 5, 'hello', 5, '***hello'],
            ['*****', 5, 5, 'hello', 5, '*****hello'],
            ['*****', -1, 5, 'hello', 5, '****hello'],
            ['*****', -3, 5, 'hello', 5, '**hello'],
            ['*****', -5, 5, 'hello', 5, 'hello'],
            ['*****', 0, 3, 'hello', 3, 'hel'],
            ['*****', 1, 6, 'hello', 5, '*hello'],
            ['*****', 0, -1, 'hello', 4, 'hell'],
            ['*****', 1, -2, 'hello', 2, '*he'],
            ['*****', 1, -2, 'hello', 2, '*he'],
            ['*****', 0, 5, '', -1, ''],
            ['*****', 2, 5, '', -1, '**'],
            ['', 0, 5, 'hello', 5, 'hello'],
            ['', -1, 5, 'hello', 5, 'hello'],
            ['', 0, 0, 'hello', 0, ''],
            ['', 0, -1, 'hello', 0, ''],
            ['*****', 0, -5, 'hello', 0, ''],
            ['*****', 0, -6, 'hello', 0, ''],
            ['*****', 1, 0, 'hello', 0, '*'],
        ];
    }

    /**
     * @dataProvider getDataForTestReadSubstringException
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Invalid offset or length.
     */
    public function testReadSubstringException($source, $offset, $length)
    {
        $stub = $this
            ->getMockBuilder('ZerusTech\Component\IO\Stream\Input\AbstractInputStream')
            ->disableOriginalClone()
            ->setMethods(['input'])
            ->getMock();

        $stub->readSubstring($source, $offset, $length);
    }

    public function getDataForTestReadSubstringException()
    {
        return [
            ['*****', 6, 5],
            ['*****', 2, null],
            ['*****', 2, false],
        ];
    }

    /**
     * @dataProvider getDataForTestSkip
     */
    public function testSkip($data, $length, $count)
    {
        $stub = $this
            ->getMockBuilder('ZerusTech\Component\IO\Stream\Input\AbstractInputStream')
            ->disableOriginalClone()
            ->setMethods(['input'])
            ->getMock();

        $stub->method('input')->will($this->returnCallback(function(&$bytes, $length) use(&$data) {$bytes = substr($data, 0, $length); $data = substr($data, $length); return 0 === strlen($bytes) ? -1 : strlen($bytes); }));

        $this->assertEquals($count, $stub->skip($length));
    }

    public function getDataForTestSkip()
    {
        return [
            ['*****', 5, 5],
            ['*****', 6, 5],
            ['*****', 3, 3],
            ['', 1, 0],
            [null, 1, 0],
            [false, 1, 0],
        ];
    }
}
