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
        $stub
            ->method('read')
            ->with(100)
            ->willReturn('hello');

       // $this->assertEquals(5, $stub->skip(100));
       $this->assertFalse($stub->isClosed());
       $stub->close();
       $this->assertTrue($stub->isClosed());
       $this->assertEquals(0, $stub->offset());
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
