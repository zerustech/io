<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For full copyright and license information, please view the LICENSE file that
 * was distributed with this source code.
 */

namespace ZerusTech\Component\IO\Stream\Output;

use ZerusTech\Component\IO\Stream\ClosableInterface;
use ZerusTech\Component\IO\Stream\FlushableInterface;
use ZerusTech\Component\IO\Exception\IOException;

/**
 * The abstract class is the superclass of all classes representing an output
 * stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
abstract class AbstractOutputStream implements OutputStreamInterface, ClosableInterface, FlushableInterface
{
    /**
     * @var bool This is a boolean that indicates if current stream has been
     * closed.
     */
    protected $closed;

    /**
     * This method creates a new output stream.
     */
    public function __construct()
    {
        $this->closed = false;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function isClosed()
    {
        return $this->closed;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function write($bytes, $offset = 0, $length = null)
    {
        $length = (null === $length ? strlen($bytes) : $length);

        if ($offset < 0 || $length < 0 || strlen($bytes) < $offset + $length) {

            throw new \OutOfBoundsException(sprintf("Invalid offset or length."));
        }

        return $this->writeBytes(substr($bytes, $offset, $length));
    }

    /**
     * This method writes all bytes in ``$bytes`` to the stream.
     *
     * @param string $bytes The bytes to write to the stream.
     * @throws IOException If an I/O error occurs.
     */
    abstract protected function writeBytes($bytes);
}
