<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For full copyright and license information, please view the LICENSE file that
 * was distributed with this source code.
 */

namespace ZerusTech\Component\IO\Tests\Stream\Input\Factory\Resolver;

use ZerusTech\Component\IO\Stream\Input\StringInputStream;
use ZerusTech\Component\IO\Stream\Input\Factory\FilterInputStreamFactory;
use ZerusTech\Component\IO\Stream\Input\Factory\Resolver\FilterInputStreamFactoryResolver;

/**
 * Test case for filter input stream resolver.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class FilterInputStreamFactoryResolverTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Input\Factory\Resolver\FilterInputStreamFactoryResolver');
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
        $resolver = new FilterInputStreamFactoryResolver($factories);
        $this->assertSame($factories, $this->factories->getValue($resolver));

        $resolver = new FilterInputStreamFactoryResolver();
        $this->assertSame([], $this->factories->getValue($resolver));
    }

    public function testResolve()
    {
        $in = new StringInputStream('hello');
        $factories = [];
        $factories[] = new FilterInputStreamFactory();
        $resolver = new FilterInputStreamFactoryResolver($factories);
        $resolved = $resolver->resolve($in);
        $this->assertInstanceOf('ZerusTech\Component\IO\Stream\Input\Factory\FilterInputStreamFactory', $resolved);
    }

    public function testAddFactory()
    {
        $factory = new FilterInputStreamFactory();
        $resolver = new FilterInputStreamFactoryResolver();
        $resolver->addFactory($factory);
        $this->assertSame([$factory], $this->factories->getValue($resolver));
    }
}
