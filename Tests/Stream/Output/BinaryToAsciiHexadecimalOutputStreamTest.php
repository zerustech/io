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

use ZerusTech\Component\IO\Stream\Output\StringOutputStream;
use ZerusTech\Component\IO\Stream\Output\BinaryToAsciiHexadecimalOutputStream;
use ZerusTech\Component\IO\Exception;

/**
 * Test case for binary to hexadecimal output stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class BinaryToHexadecimalOutputStreamTest extends \PHPUnit_Framework_TestCase
{
    public function testWrite()
    {
        $out = new StringOutputStream();

        $stream = new BinaryToAsciiHexadecimalOutputStream($out);

        $stream->write('hello');

        $this->assertEquals('68656C6C6F', $out->__toString());
    }
}
