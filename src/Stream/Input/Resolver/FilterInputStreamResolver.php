<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For full copyright and license information, please view the LICENSE file that
 * was distributed with this source code.
 */

namespace ZerusTech\Component\IO\Stream\Input\Resolver;

use ZerusTech\Component\IO\Stream\Input\InputStreamInterface;
use ZerusTech\Component\IO\Stream\Input\BufferedInputStream;
use ZerusTech\Component\IO\Stream\Input\Factory\FilterInputStreamFactoryInterface;

/**
 * This class holds a list of filter input stream factory instances.
 * When it receives an input stream, it finds the first factory that
 * claims to support the data format and asks the factory to create a filter
 * input stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class FilterInputStreamResolver implements FilterInputStreamResolverInterface
{
    /**
     * @var FilterInputStreamFactoryInterface[] The internal list of factories.
     */
    private $factories;

    /**
     * This method creates a new filter input stream resolver and initializes
     * its internal list of factories with the given argument.
     *
     * @param FilterInputStreamFactoryInterface[] The list of factories.
     */
    public function __construct($factories = null)
    {
        $this->factories = is_array($factories) ? $factories : [];
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(InputStreamInterface $input)
    {
        $resolvedStream = null;

        $resolvedFactory = null;

        if (false === $input->markSupported()) {

            $input = new BufferedInputStream($input);
        }

        foreach ($this->factories as $factory) {

            if (true === $factory->support($input)) {

                $resolvedFactory = $factory;

                break;
            }
        }

        if (null !== $resolvedFactory) {

            $resolvedStream = $resolvedFactory->create($input);
        }

        return $resolvedStream;
    }

    /**
     * {@inheritdoc}
     */
    public function addFactory(FilterInputStreamFactoryInterface $factory)
    {
        $this->factories[] = $factory;
    }
}
