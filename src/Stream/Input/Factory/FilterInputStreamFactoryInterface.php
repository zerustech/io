<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For full copyright and license information, please view the LICENSE file that
 * was distributed with this source code.
 */

namespace ZerusTech\Component\IO\Stream\Input\Factory;

use ZerusTech\Component\IO\Exception\IOException;
use ZerusTech\Component\IO\Stream\Input\InputStreamInterface;

/**
 * This interface creates filter input stream objects of a specific class.
 *
 * It also provides a method that indicates if the filter input stream it
 * created supports the data format of a given input stream.
 *
 * It's typically used by a filter input stream factory resolver for resolving a
 * suitable factory.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
interface FilterInputStreamFactoryInterface
{
    /**
     * This method creates a specific type of filter input stream that reads and
     * converts data from the ``$input`` stream.
     *
     * @param InputStreamInterface $input The subordinate input stream.
     *
     * @return FilterInputStream The filter input stream created.
     */
    public function create(InputStreamInterface $input);

    /**
     * This method returns a boolean that indicates whether the data format of
     * the subordinate input stream is supported by the filter input stream
     * created by this factory.
     *
     * @param InputStreamInterface $input The subordinate input stream.
     * @return bool True if the data format is supported, false otherwise.
     * @throws IOException if ``$input`` does not support mark and reset.
     */
    public function support(InputStreamInterface $input);
}
