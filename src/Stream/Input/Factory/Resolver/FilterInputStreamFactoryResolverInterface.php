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
use ZerusTech\Component\IO\Stream\Input\Factory\FilterInputStreamFactoryInterface;

/**
 * This interface detects the data format of the input stream and resolves it
 * to a filter input stream factory that creates suitable filter input stream
 * objects.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
interface FilterInputStreamFactoryResolverInterface
{
    /**
     * Resolves the input stream to a filter input stream factory.
     *
     * @param InputStreamInterface $input the input stream.
     * @return FilterInputStreamFactoryInterface|null The filter stream factory,
     * or null if the data format is not supported.
     */
    public function resolve(InputStreamInterface $input);

    /**
     * Adds the given factory to the internal list of factories.
     *
     * @param FilterInputStreamFactoryInterface $factory The filter input stream
     * factory.
     * @return void
     */
    public function addFactory(FilterInputStreamFactoryInterface $factory);
}
