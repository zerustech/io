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

use ZerusTech\Component\IO\Stream\Output\FilterOutputStream;
use ZerusTech\Component\IO\Exception;

/**
 * Test case for filter output stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class FilterOutputStreamTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Output\FilterOutputStream');

        $this->out = $this->ref->getProperty('out');
        $this->out->setAccessible(true);

        $this->output = $this->ref->getMethod('output');
        $this->output->setAccessible(true);
    }

    public function testConstructor()
    {
        $output = $this->getMockForAbstractClass('ZerusTech\Component\IO\Stream\Output\AbstractOutputStream');
        $instance = new FilterOutputStream($output);
        $this->assertSame($output, $this->out->getValue($instance));
    }

    public function testProxyMethods()
    {
        $output = $this->getMockBuilder('ZerusTech\Component\IO\Stream\Output\AbstractOutputStream')
            ->setMethods(['flush', 'close', 'isClosed', 'write', 'writeSubstring', 'output'])
            ->getMock();

        $output->expects($this->once())->method('output')->with('hello')->willReturn(5);
        $output->expects($this->exactly(2))->method('flush')->will($this->returnSelf());
        $output->expects($this->once())->method('close')->will($this->returnSelf());
        $output->expects($this->exactly(2))->method('isClosed')->will($this->onConsecutiveCalls(false, true));

        $instance = new FilterOutputStream($output);

        $this->output->invoke($instance, 'hello');
        $this->assertFalse($instance->isClosed());
        $this->assertSame($instance, $instance->flush());
        $this->assertSame($instance, $instance->close());
        $this->assertTrue($instance->isClosed());
    }
}
