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
        $this->stub = $this->getMockForAbstractClass('ZerusTech\Component\IO\Stream\Output\AbstractOutputStream', []);
    }

    public function tearDown()
    {
        $this->stub = null;
    }

    /**
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessage Stream is already closed, can't be closed again.
     */
    public function testDummyMethods()
    {
        $this->assertFalse($this->stub->isClosed());

        $this->stub->close();

        $this->assertTrue($this->stub->isClosed());

        $this->stub->close();
    }
}
