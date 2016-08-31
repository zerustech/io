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
    public function write($bytes)
    {
        return $this->writeSubstring($bytes, 0, strlen($bytes));
    }

    /**
     * {@inheritdoc}
     */
    public function writeSubstring($bytes, $offset, $length)
    {
        $offset = 0 > $offset ? max(0, strlen($bytes) + $offset) : $offset;

        $length = 0 > $length ? max(0, strlen($bytes) - $offset + $length) : $length;

        if (strlen($bytes) > 0 && $offset >= strlen($bytes) || null === $length || false === $length) {

            throw new \OutOfBoundsException(sprintf("Invalid offset or length."));
        }

        if (true === $this->closed) {

            throw new IOException(sprintf("Stream is already closed, can't be written."));
        }

        return $this->output((string)substr($bytes, $offset, $length));
    }

    /**
     * This method writes all bytes in ``$bytes`` to the actual target of
     * current stream. It is called by the ``writeSubstring()`` method.
     *
     * Subclasses of abstract output stream should override this method with the
     * actual logic for the manupulation of the byte data.
     *
     * @param string $bytes The bytes to write to the stream.
     * @return int The actual number of bytes written to the stream.
     * @throws IOException If an I/O error occurs.
     */
    abstract protected function output($bytes);
}
