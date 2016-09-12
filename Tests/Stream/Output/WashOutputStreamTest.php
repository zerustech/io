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

use ZerusTech\Component\IO\Exception;
use ZerusTech\Component\IO\Stream\Output\StringOutputStream;
use ZerusTech\Component\IO\Stream\Input\FileInputStream;
use ZerusTech\Component\IO\Stream\Output\FileOutputStream;
use ZerusTech\Component\IO\Stream\Output\WashOutputStream;

/**
 * Test case for wash output stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class WashOutputStreamTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Output\WashOutputStream');

        $this->output = $this->ref->getMethod('output');
        $this->output->setAccessible(true);

        $this->searchPattern = $this->ref->getProperty('searchPattern');
        $this->searchPattern->setAccessible(true);

        $this->replacePattern = $this->ref->getProperty('replacePattern');
        $this->replacePattern->setAccessible(true);

        $this->out = $this->ref->getProperty('out');
        $this->out->setAccessible(true);
    }

    public function tearDown()
    {
        $this->out = null;
        $this->output = null;
        $this->ref = null;
    }

    /**
     * @dataProvider getDataForTestConstructor
     */
    public function testConstructor($search, $replace)
    {
        $out = new StringOutputStream();
        $stream = new WashOutputStream($out, $search, $replace);
        $this->assertSame($out, $this->out->getValue($stream));
        $this->assertEquals($search, $this->searchPattern->getValue($stream));
        $this->assertEquals($replace, $this->replacePattern->getValue($stream));
    }

    public function getDataForTestConstructor()
    {
        return [
            ["/([^\n\t ]*)([\n\t ]*)([^\n\t ]*)/", "$1$3"],
            ["/([^ ]*)([ ]*)([^ ]*)/", "$1$3"],
        ];
    }

    /**
     * @dataProvider getDataForTestOutput
     */
    public function testOutput($hex, $expected, $count)
    {
        $out = new StringOutputStream();
        $stream = new WashOutputStream($out);
        $this->assertEquals($count, $this->output->invoke($stream, $hex));
        $this->assertEquals($expected, $out->__toString());
    }

    public function getDataForTestOutput()
    {
        return [
            ["6\n8\t6\r5 6C6C6F\n","68656C6C6F", 10],
            ["68656C6C6F\n68656C", "68656C6C6F68656C", 16],
            ["68656C6C6F\n68656C6C6F\n68656C", "68656C6C6F68656C6C6F68656C", 26],
            ["68656C6C\n6F68656C6C\n6F68656C", "68656C6C6F68656C6C6F68656C", 26],
            ["68656C\n6C6F68656C\n6C6F68656C\n", "68656C6C6F68656C6C6F68656C", 26],
            ["6865\n6C6C6F6865\n6C6C6F6865\n6C", "68656C6C6F68656C6C6F68656C", 26],
            ["68\n656C6C6F68\n656C6C6F68\n656C", "68656C6C6F68656C6C6F68656C", 26],
            ["68656C6C6F\n68656C6C6F\n68656C", "68656C6C6F68656C6C6F68656C", 26],
        ];
    }
}
