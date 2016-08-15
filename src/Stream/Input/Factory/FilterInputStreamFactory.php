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
use ZerusTech\Component\IO\Stream\Input\FilterInputStream;
use ZerusTech\Component\IO\Stream\Input\InputStreamInterface;

/**
 * This class creates ``FilterInputStream`` instances that support any data
 * format.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class FilterInputStreamFactory implements FilterInputStreamFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(InputStreamInterface $input)
    {
        return new FilterInputStream($input);
    }

    /**
     * {@inheritdoc}
     */
    public function support(InputStreamInterface $input)
    {
        if (false === $input->markSupported()) {

            throw new IOException(sprintf("Class %s does not support mark().", get_class($input)));
        }

        return true;
    }
}
