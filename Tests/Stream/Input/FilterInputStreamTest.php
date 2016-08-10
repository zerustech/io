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

use ZerusTech\Component\IO\Stream\Input\FilterInputStream;
use ZerusTech\Component\IO\Exception;

/**
 * Test case for filter input stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class FilterInputStreamTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $input = $this->getMockForAbstractClass('ZerusTech\Component\IO\Stream\Input\AbstractInputStream', [null]);
        $instance = new FilterInputStream($input);
        $reflection = new \ReflectionClass('ZerusTech\Component\IO\Stream\Input\FilterInputStream');
        $in = $reflection->getProperty('in');
        $in->setAccessible(true);
        $this->assertSame($input, $in->getValue($instance));
    }

    public function testProxyMethods()
    {
        $input = $this->getMockBuilder('ZerusTech\Component\IO\Stream\Input\AbstractInputStream')
                      ->setMethods(['read', 'available', 'mark', 'markSupported', 'reset', 'skip', 'close', 'getResource', 'isClosed'])
                      ->getMock();

        $instance = new FilterInputStream($input);

        $input->method('read')->willReturn('hello');
        $input->method('available')->willReturn(0);
        $input->method('mark')->with(10);
        $input->method('markSupported')->willReturn(false);
        $input->method('reset')->willReturn(null);
        $input->method('skip')->with(100)->willReturn(50);
        $input->method('getResource')->willReturn(null);
        $input->method('isClosed')->willReturn(false);
        $input->method('close')->willReturn(null);

        $this->assertEquals('hello', $instance->read(100));
        $this->assertEquals(0, $instance->available());
        $this->assertNull($instance->mark(10));
        $this->assertFalse($instance->markSupported());
        $this->assertNull($instance->reset());
        $this->assertEquals(50, $instance->skip(100));
        $this->assertNull($instance->getResource());
        $this->assertFalse($instance->isClosed());
        $this->assertNull($instance->close());
    }
}
