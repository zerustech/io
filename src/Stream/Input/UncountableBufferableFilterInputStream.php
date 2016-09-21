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
 * This class is the super class of all bufferable filter input streams that
 * it's impossible to count the exact number of bytes available for them until
 * the EOF has been reached.
 *
 * This is conceptually similar to the uncountable filter input stream, except
 * that this class also checks the availability of the internal buffer.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class UncountableBufferableFilterInputStream extends BufferableFilterInputStream
{
    /**
     * This method creates a new uncountable bufferable filter input stream.
     */
    public function __construct(InputStreamInterface $in, $readBufferSize = 1024)
    {
        parent::__construct($in, $readBufferSize);
    }

    /**
     * {@inheritdoc}
     *
     * This methods returns 1 if either the internal buffer or the subordinate
     * input stream is still available, or 0 otherwise.
     *
     * @return int 1 if there is any byte available for reading.
     */
    public function available()
    {
        return (strlen($this->buffer) + $this->in->available()) > 0 ? 1 : 0;
    }
}
