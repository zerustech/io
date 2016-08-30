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
     * Create a new input stream instance.
     */
    public function __construct()
    {
        $this->closed = false;
    }

    /**
     * {@inheritdoc}
     */
    public function read(&$bytes, $length = 1)
    {
        return $this->readSubstring($bytes, 0, $length);
    }

    /**
     * {@inheritdoc}
     */
    public function readSubstring(&$bytes, $offset, $length)
    {
        $offset = $offset < 0 ? max(0, strlen($bytes) + $offset) : $offset;

        $length = $length < 0 ? max(0, strlen($bytes) - $offset + $length) : $length;

        if ($offset > strlen($bytes) || 0 === $length || null === $length || false === $length) {

            throw new \OutOfBoundsException(sprintf("Invalid offset or length."));
        }

        if (true === $this->closed) {

            throw new IOException(sprintf("Stream is already closed, can't be read."));
        }

        $count = $this->input($data, $length);

        $bytes = substr($bytes, 0, $offset).$data;

        return $count;
    }

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
    public function mark($limit)
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
     * {@inheritdoc}
     */
    public function skip($length, $buffer = 1024)
    {
        $remaining = $length;

        while ($remaining > 0) {

            $bufferSize = min($buffer, $length, $remaining);

            $numberOfBytes = $this->input($bytes, $bufferSize);

            if (-1 === $numberOfBytes) {

                break;
            }

            $remaining -= $numberOfBytes;
        }

        return $length - $remaining;
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
     * This method reads ``$length`` bytes from the actual source of current
     * stream and stores the bytes read into the caller supplied buffer. The
     * actual number of bytes read is returned as an int. A -1 is returned to
     * indicate the end of the stream.
     *
     * NOTE: The actual number of bytes read does not always equal to the lenght
     * of ``$bytes``. For example, sometimes, a few bytes will be dropped from
     * the result, so the number of bytes read is greater than the lenght of
     * ``$bytes``.
     *
     * Subclasses of abstract input stream should override this method with the
     * actual logic for manuplulating the byte data.
     *
     * @param string $bytes The buffer into which the bytes read will be stored.
     * @param int $length The requested number of bytes to read.
     * @return int The actual number of bytes read or -1 if end of stream.
     * @throws IOException If an I/O error occurs.
     */
    abstract protected function input(&$bytes, $length);
}
