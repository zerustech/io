<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For full copyright and license information, please view the LICENSE file that
 * was distributed with this source code.
 */
namespace ZerusTech\Component\IO\Tests\Exception;

use ZerusTech\Component\IO\Exception;

/**
 * Test case for io exception.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class IOExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $previous = new \Exception('The previous exception', 1, null);
        $exception = new Exception\IOException('The test I/O exception', 2, $previous);

        $this->assertEquals('The test I/O exception', $exception->getMessage());
        $this->assertEquals(2, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
