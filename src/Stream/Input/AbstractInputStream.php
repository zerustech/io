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
     * @var int The index of the next byte that will be read from the stream.
     */
    protected $position;

    /**
     * Create a new input stream instance.
     */
    public function __construct()
    {
        $this->closed = false;

        $this->position = 0;
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

        if ($offset > strlen($bytes) || null === $length || false === $length) {

            throw new \OutOfBoundsException(sprintf("Invalid offset or length."));
        }

        if (true === $this->closed) {

            throw new IOException(sprintf("Stream is already closed, can't be read."));
        }

        $data = '';

        $count = $length > 0 ? $this->input($data, $length) : $length;

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
     * This method skips the specified number of bytes in the stream. It retruns
     * the actual number of bytes skipped, which may be less than the requred
     * amount.
     *
     * @param int $length The requested number of bytes to skip.
     * @return int The actual number of bytes skipped.
     * @throws IOException If an error occurs.
     */
    public function skip($length)
    {
        $remaining = $length;

        $bufferSize = min(2048, $length);

        while ($remaining > 0) {

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

        $this->position = 0;

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
     * Returns the index of the next byte in current stream.
     *
     * @return int The index of the next byte.
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * This method reads ``$length`` bytes from the actual source of current
     * stream and stores the bytes read into the caller supplied buffer. The
     * actual number of bytes read is returned as an int. A -1 is returned to
     * indicate the end of the stream.
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
