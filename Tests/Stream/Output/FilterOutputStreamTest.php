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
    public function testConstructor()
    {
        $output = $this->getMockForAbstractClass('ZerusTech\Component\IO\Stream\Output\AbstractOutputStream', []);
        $instance = new FilterOutputStream($output);
        $reflection = new \ReflectionClass('ZerusTech\Component\IO\Stream\Output\FilterOutputStream');
        $out = $reflection->getProperty('out');
        $out->setAccessible(true);
        $this->assertSame($output, $out->getValue($instance));
    }

    public function testProxyMethods()
    {
        $output = $this->getMockBuilder('ZerusTech\Component\IO\Stream\Output\AbstractOutputStream')
            ->setMethods(['flush', 'write', 'close', 'isClosed'])
            ->getMock();

        $output->expects($this->once())->method('write')->with('abc');
        $output->expects($this->exactly(2))->method('flush');
        $output->expects($this->once())->method('close');

        $instance = new FilterOutputStream($output);

        $this->assertSame($instance, $instance->write('abc'));
        $this->assertSame($instance, $instance->flush());
        $this->assertSame($instance, $instance->close());
    }
}
