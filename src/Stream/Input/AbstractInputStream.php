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

use ZerusTech\Component\IO\Stream\ClosableInterface;
use ZerusTech\Component\IO\Exception\IOException;

/**
 * The abstract class is the superclass of all classes representing an input
 * stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
abstract class AbstractInputStream implements InputStreamInterface, ClosableInterface
{
    /**
     * @var bool A boolean that indicates whether current stream is closed.
     */
    protected $closed;

    /**
     * @var int The global offset.
     */
    protected $offset;

    /**
     * Create a new input stream instance.
     */
    public function __construct()
    {
        $this->closed = false;

        $this->offset = 0;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function read($length = 1);

    /**
     * {@inheritdoc}
     */
    public function available()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function mark($readLimit)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function markSupported()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        throw new IOException(sprintf("%s", "mark/reset not supported."));
    }

    /**
     * This method skips the specified number of bytes in the stream. It retruns
     * the actual number of bytes skipped, which may be less than the requred
     * amount.
     *
     * @param int $byteCount The requested number of bytes to skip.
     * @return int The actual number of bytes skipped.
     * @throws IOException If an error occurs.
     */
    public function skip($byteCount)
    {
        $offset = $this->offset;
        $this->read($byteCount);
        return $this->offset - $offset;
    }

    /**
     * Closes current stream.
     */
    public function close()
    {
        if (true === $this->closed) {

            throw new IOException(sprintf("Stream is already closed, can't be closed again."));
        }

        $this->closed = true;

        return $this;
    }

    /**
     * Checks whether current stream is closed or not.
     *
     * @return bool True if current stream is closed, and false otherwise.
     */
    public function isClosed()
    {
        return $this->closed;
    }

    /**
     * {@inheritdoc}
     */
    public function offset()
    {
        return $this->offset;
    }
}
