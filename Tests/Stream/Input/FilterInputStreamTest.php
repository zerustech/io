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
use ZerusTech\Component\IO\Stream\Input\StringInputStream;
use ZerusTech\Component\IO\Exception;

/**
 * Test case for filter input stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class FilterInputStreamTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Input\FilterInputStream');

        $this->in = $this->ref->getProperty('in');
        $this->in->setAccessible(true);

        $this->input = $this->ref->getMethod('input');
        $this->input->setAccessible(true);
    }

    public function tearDown()
    {
        $this->in = null;
        $this->ref = null;
    }

    public function testConstructor()
    {
        $input = $this->getMockForAbstractClass('ZerusTech\Component\IO\Stream\Input\AbstractInputStream', []);
        $instance = new FilterInputStream($input);
        $reflection = new \ReflectionClass('ZerusTech\Component\IO\Stream\Input\FilterInputStream');
        $this->assertSame($input, $this->in->getValue($instance));
    }

    public function testProxyMethods()
    {
        $input = $this->getMockBuilder('ZerusTech\Component\IO\Stream\Input\AbstractInputStream')
                      ->setMethods(['input', 'available', 'mark', 'markSupported', 'reset', 'close'])
                      ->getMock();

        $instance = new FilterInputStream($input);

        $data = "hello";

        $input->expects($this->exactly(2))->method('input')->will($this->returnCallback(
            function(&$bytes, $length) use (&$data) {
                $bytes = substr($data, 0, $length);
                $data = substr($data, $length);
                return 0 === strlen($bytes) ? -1 : strlen($bytes);
            }));

        $input->expects($this->once())->method('available')->willReturn(strlen($data));
        $input->expects($this->once())->method('mark')->with(5)->will($this->returnSelf());
        $input->expects($this->once())->method('markSupported')->willReturn(false);
        $input->expects($this->once())->method('reset')->will($this->returnSelf());
        $input->expects($this->once())->method('close')->will($this->returnSelf());

        $this->assertEquals(5, $instance->available());
        $this->assertSame($instance, $instance->mark(5));
        $this->assertFalse($instance->markSupported());
        $this->assertSame($instance, $instance->reset());
        $this->assertEquals(1, $instance->skip(1));
        $this->assertEquals(4, $this->input->invokeArgs($instance, [&$bytes, 4]));
        $this->assertEquals('ello', $bytes);
        $this->assertFalse($instance->isClosed());
        $this->assertSame($instance, $instance->close());
        $this->assertTrue($instance->isClosed());
    }
}
