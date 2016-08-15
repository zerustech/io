<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For full copyright and license information, please view the LICENSE file that
 * was distributed with this source code.
 */

namespace ZerusTech\Component\IO\Tests\Stream\Input\Resolver;

use ZerusTech\Component\IO\Stream\Input\StringInputStream;
use ZerusTech\Component\IO\Stream\Input\Factory\FilterInputStreamFactory;
use ZerusTech\Component\IO\Stream\Input\Resolver\FilterInputStreamResolver;

/**
 * Test case for filter input stream resolver.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class FilterInputStreamResolverTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Input\Resolver\FilterInputStreamResolver');
        $this->factories = $this->ref->getProperty('factories');
        $this->factories->setAccessible(true);
    }

    public function tearDown()
    {
        $this->factories = null;
        $this->ref = null;
    }

    public function testConstructor()
    {
        $factories = [];
        $factories[] = new FilterInputStreamFactory();
        $resolver = new FilterInputStreamResolver($factories);
        $this->assertSame($factories, $this->factories->getValue($resolver));

        $resolver = new FilterInputStreamResolver();
        $this->assertSame([], $this->factories->getValue($resolver));
    }

    public function testResolve()
    {
        $in = new StringInputStream('hello');
        $factories = [];
        $factories[] = new FilterInputStreamFactory();
        $resolver = new FilterInputStreamResolver($factories);
        $resolved = $resolver->resolve($in);
        $this->assertInstanceOf('ZerusTech\Component\IO\Stream\Input\FilterInputStream', $resolved);
    }

    public function testAddFactory()
    {
        $factory = new FilterInputStreamFactory();
        $resolver = new FilterInputStreamResolver();
        $resolver->addFactory($factory);
        $this->assertSame([$factory], $this->factories->getValue($resolver));
    }
}
