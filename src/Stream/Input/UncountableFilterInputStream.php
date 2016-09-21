<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For full copyright and license information, please view the LICENSE file that
 * was distributed with this source code.
 */

namespace ZerusTech\Component\IO\Stream\Input;

/**
 * This class is the super class of all filter input streams that it's
 * impossible to count the exact number of bytes available for them until the
 * EOF has been reached.
 *
 * For example, for an input stream that converts ascii hexadecimal to binary
 * bytes, it's impossible to predict the exact number of binary bytes available
 * because there could be space characters in the subordinate stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class UncountableFilterInputStream extends FilterInputStream
{
    /**
     * This method creates a new uncountable filter input stream.
     *
     * @param InputStreamInterface $in The subordinate input stream.
     */
    public function __construct(InputStreamInterface $in)
    {
        parent::__construct($in);
    }

    /**
     * {@inheritdoc}
     *
     * This methods returns 1 if the subordinate input stream is still
     * available, or 0 otherwise.
     *
     * @return int 1 if the subordinate input stream is till available, or 0
     * otherwise.
     */
    public function available()
    {
        return $this->in->available() > 0 ? 1 : 0;
    }
}
