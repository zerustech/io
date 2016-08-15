<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For full copyright and license information, please view the LICENSE file that
 * was distributed with this source code.
 */

namespace ZerusTech\Component\IO\Tests\Stream\Input\Factory;

use ZerusTech\Component\IO\Stream\Input\BufferedInputStream;
use ZerusTech\Component\IO\Stream\Input\FilterInputStream;
use ZerusTech\Component\IO\Stream\Input\StringInputStream;
use ZerusTech\Component\IO\Stream\Input\Factory\FilterInputStreamFactory;

/**
 * Test case for filter input stream factory.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class FilterInputStreamFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $in = new StringInputStream("hello");

        $factory = new FilterInputStreamFactory();

        $stream = $factory->create($in);

        $this->assertInstanceOf('ZerusTech\Component\IO\Stream\Input\FilterInputStream', $stream);
    }

    public function testSupport()
    {
        $factory = new FilterInputStreamFactory();

        $in = new BufferedInputStream(new StringInputStream("hello"));

        $this->assertTrue($factory->support($in));
    }

    /**
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessageRegex /Class [^ ]+ does not support mark()./
     */
    public function testSupportWithException()
    {
        $factory = new FilterInputStreamFactory();

        $in = new StringInputStream('hello');

        $factory->support($in);
    }
}
