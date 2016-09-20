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

use ZerusTech\Component\IO\Exception;
use ZerusTech\Component\IO\Stream\Input\StringInputStream;
use ZerusTech\Component\IO\Stream\Input\UnpredictableFilterInputStream;

/**
 * Test case for  input stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class UnpredictableFilterInputStreamTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Input\UnpredictableFilterInputStream');

        $this->in = $this->ref->getProperty('in');
        $this->in->setAccessible(true);
    }

    public function tearDown()
    {
        $this->in = null;
        $this->ref = null;
    }

    public function testConstructor()
    {
        $in = new StringInputStream('hello');
        $stream = new UnpredictableFilterInputStream($in);
        $this->assertSame($in, $this->in->getValue($stream));
    }

    public function testAvailable()
    {
        $in = new StringInputStream('hello');
        $stream = new UnpredictableFilterInputStream($in);
        $this->assertEquals(1, $stream->available());
        $this->assertEquals(5, $stream->read($bytes, 5));
        $this->assertEquals('hello', $bytes);
        $this->assertEquals(0, $stream->available());
    }
}
