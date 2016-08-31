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

/**
 * Test case for abstract output stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class AbstractOutputStreamTest extends \PHPUnit_Framework_TestCase
{

    public function setup()
    {
        $this->ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Output\AbstractOutputStream');

        $this->closed = $this->ref->getProperty('closed');
        $this->closed->setAccessible(true);

        $this->output = $this->ref->getMethod('output');
        $this->output->setAccessible(true);
    }

    public function tearDown()
    {
        $this->output = null;

        $this->ref = null;
    }

    public function testConstructor()
    {
        $stub = $this->getMockForAbstractClass('ZerusTech\Component\IO\Stream\Output\AbstractOutputStream', []);

        $this->assertFalse($this->closed->getValue($stub));
    }

    public function testWrite()
    {
        $stub = $this->getMockBuilder('ZerusTech\Component\IO\Stream\Output\AbstractOutputStream')->setMethods(['writeSubstring', 'output'])->getMock();
        $stub->expects($this->once())->method('writeSubstring')->with('hello', 0, 5)->willReturn(5);
        $this->assertEquals(5, $stub->write('hello'));
    }

    /**
     * @dataProvider getDataForTestWriteSubstring
     */
    public function testWriteSubstring($sourceBytes, $offset, $length, $actualBytes)
    {
        $stub = $this->getMockBuilder('ZerusTech\Component\IO\Stream\Output\AbstractOutputStream')->setMethods(['output'])->getMock();
        $stub->expects($this->once())->method('output')->with($actualBytes)->willReturn(strlen($actualBytes));
        $this->assertEquals(strlen($actualBytes), $stub->writeSubstring($sourceBytes, $offset, $length));
    }

    public function getDataForTestWriteSubstring()
    {
        return [
            ['hello', 0, 5, 'hello'],
            ['hello', 2, 3, 'llo'],
            ['hello', 2, 4, 'llo'],
            ['hello', -1, 1, 'o'],
            ['hello', -3, 3, 'llo'],
            ['hello', -3, 4, 'llo'],
            ['hello', -5, 5, 'hello'],
            ['hello', -6, 5, 'hello'],
            ['hello', 0, -1, 'hell'],
            ['hello', 0, -3, 'he'],
            ['hello', 1, -1, 'ell'],
            ['hello', 1, -2, 'el'],
            ['hello', 1, -2, 'el'],
            ['', 0, 0, ''],
            ['', 0, 3, ''],
            ['hello', 0, 0, ''],
            ['hello', 0, -5, ''],
            ['hello', 1, -4, ''],
            ['hello', 1, -5, ''],
            ['hello', -1, 0, ''],
            ['hello', -2, -2, ''],
            ['hello', -3, -4, ''],
        ];
    }

    /**
     * @dataProvider getDataForTestWriteSubstringException
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Invalid offset or length.
     */
    public function testWriteSubstringException($sourceBytes, $offset, $length)
    {
        $stub = $this->getMockBuilder('ZerusTech\Component\IO\Stream\Output\AbstractOutputStream')->setMethods(['output'])->getMock();
        $stub->writeSubstring($sourceBytes, $offset, $length);
    }

    public function getDataForTestWriteSubstringException()
    {
        return [
            ['hello', 5, 1],
            ['hello', 0, false],
            ['hello', 0, null],
       ];
    }

    /**
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessage Stream is already closed, can't be closed again.
     */
    public function testMiscMethods()
    {
        $stub = $this->getMockForAbstractClass('ZerusTech\Component\IO\Stream\Output\AbstractOutputStream', []);

        $this->assertSame($stub, $stub->flush());

        $this->assertFalse($stub->isClosed());

        $stub->close();

        $this->assertTrue($stub->isClosed());

        $stub->close();
    }
}
