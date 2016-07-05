<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For full copyright and license information, please view the LICENSE file that
 * was distributed with this source code.
 */
namespace ZerusTech\Component\IO\Tests\Stream;

use ZerusTech\Component\IO\Stream\AbstractStream;

/**
 * Test case for abstract stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class AbstractStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getDataForConstructor
     */
    public function testConstructor($closed, $source, $mode)
    {
        $resource = @fopen($source, $mode);

        $stub = $this->getMockForAbstractClass("ZerusTech\\Component\\IO\\Stream\\AbstractStream", [$resource]);

        $this->assertSame($resource, $stub->getResource());

        $this->assertEquals($closed, $stub->isClosed());

        @fclose($resource);
    }

    public function getDataForConstructor()
    {
        return [
            [false, 'php://memory', 'rb'],
            [true, 'php://nofile', 'rb']
        ];
    }

    public function testClose()
    {
        $resource = fopen('php://memory', 'rb');
        $stub = $this->getMockForAbstractClass("ZerusTech\\Component\\IO\\Stream\\AbstractStream", [$resource]);
        $stub->close();
    }
}
