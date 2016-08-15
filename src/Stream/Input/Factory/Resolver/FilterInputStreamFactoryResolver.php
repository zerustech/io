<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For full copyright and license information, please view the LICENSE file that
 * was distributed with this source code.
 */

namespace ZerusTech\Component\IO\Stream\Input\Factory\Resolver;

use ZerusTech\Component\IO\Stream\Input\InputStreamInterface;
use ZerusTech\Component\IO\Stream\Input\BufferedInputStream;
use ZerusTech\Component\IO\Stream\Input\Factory\FilterInputStreamFactoryInterface;

/**
 * This class holds a list of filter input stream factory instances.
 * When it receives an input stream, it resolves it to the first factory that
 * claims to support the data format.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class FilterInputStreamFactoryResolver implements FilterInputStreamFactoryResolverInterface
{
    /**
     * @var FilterInputStreamFactoryInterface[] The internal list of factories.
     */
    private $factories;

    /**
     * This method creates a new filter input stream factory resolver and
     * initializes its internal list of factories with the given argument.
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
        $resolved = null;

        if (false === $input->markSupported()) {

            $input = new BufferedInputStream($input);
        }

        foreach ($this->factories as $factory) {

            if (true === $factory->support($input)) {

                $resolved = $factory;

                break;
            }
        }

        return $resolved;
    }

    /**
     * {@inheritdoc}
     */
    public function addFactory(FilterInputStreamFactoryInterface $factory)
    {
        $this->factories[] = $factory;
    }
}
