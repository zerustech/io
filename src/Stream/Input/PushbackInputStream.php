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

use ZerusTech\Component\IO\Exception\IOException;

/**
 * This subclass of ``BufferableFilterInputStream`` provides the ability to
 * unread data from a stream. It maintains an internal buffer of unread data
 * that is supplied to the next read operation. This is conceptually similar to
 * mark/reset functionality, except that in this case the position to reset the
 * stream to does not need to be known
 *
 * NOTE: in this class, the internal buffer is used to store the pushed back
 * bytes, not the pre-fetched bytes.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class PushbackInputStream extends BufferableFilterInputStream
{
    /**
     * @var int The maximum number of bytes that the internal buffer can store.
     */
    private $bufferSize = 1;

    /**
     * Create a filter input stream instance with the specified subordinate
     * input stream.
     *
     * @param InputStreamInterface $in The subordinate input stream.
     * @param int $bufferSize The maximum number of bytes that the internal
     * buffer can store.
     * @param int $readBufferSize The maximum number of bytes to be pre-fetched.
     *
     * @throws \InvalidArgumentException When the buffer size is less than 1.
     */
    public function __construct(InputStreamInterface $in, $bufferSize = 1, $readBufferSize = 1024)
    {
        if ($bufferSize <= 0) {

            throw new \InvalidArgumentException(sprintf("The buffer size must be greater than %d.", 0));
        }

        parent::__construct($in, $readBufferSize);

        $this->bufferSize = $bufferSize;
    }

    /**
     * Pushes the provided string back to the internal buffer.
     *
     * This method operates by calling ``unreadSubstring()`` method like so:
     * ``$this->unreadSubstring($bytes, 0, strlen($bytes));``.
     *
     * @param string $bytes The string to be pushed back.
     *
     * @throws IOException If there is insufficient space in the internal
     * buffer.
     */
    public function unread($bytes)
    {
        return $this->unreadSubstring($bytes, 0, strlen($bytes));
    }

    /**
     * This method tries to push a substring of ``$bytes``, specified by
     * ``$offset`` and ``$length``, to the internal buffer.
     *
     * Firstly:
     *
     * If ``$offset >= 0``, the bytes read start to store at offset'th position
     * in the string, counting from zero, left to right.
     *
     * If ``$offset < 0``, ``$offset = max(0, strlen($bytes) + $offset)``.
     *
     * If ``$length > 0``, up to ``$length`` bytes will be read and stored in
     * the buffer.
     *
     * If ``$length < 0``,
     * ``$length = max(0, strlen($bytes) - $offset + $length)``.
     *
     * Finally:
     *
     * If ``strlen($bytes) > 0 && $offset >= strlen($bytes)``, an out of bounds exception is thrown.
     *
     * If ``$length`` is null or false, an out of bounds exception is thrown.
     *
     * If ``$length`` is 0, nothing is pushed back.
     *
     * If ``strlen($bytes) === 0 and $offset === 0``, nothing is pushed back.
     *
     * So basically, the logic is as follows:
     *
     *     $offset = 0 > $offset ? max(0, strlen($bytes) + $offset) : $offset;``
     *
     *     $length = 0 > $length ? max(0, strlen($bytes) - $offset + $length) : $length;``
     *
     *     if (strlen($bytes) > 0 && $offset >= strlen($bytes) || null === $length || false === $length) {
     *
     *         throw new \OutOfBoundsException(...);
     *     }
     *
     * @param string $bytes The source of the substring to be pushed back.
     * @param int $offset The starting index of the substring.
     * @param int $length The length of the substring.
     *
     * @throws IOException If there is insufficient space in the internal
     * buffer.
     */
    public function unreadSubstring($bytes, $offset, $length)
    {
        $offset = $offset < 0 ? max(0, strlen($bytes) + $offset) : $offset;

        $length = $length < 0 ? max(0, strlen($bytes) - $offset + $length) : $length;

        if (strlen($bytes) > 0 && $offset >= strlen($bytes) || null === $length || false === $length) {

            throw new \OutOfBoundsException(sprintf("Invalid offset or length."));
        }

        if (true === $this->closed) {

            throw new IOException(sprintf("Stream is already closed, can't be read."));
        }

        return $this->pushback(substr($bytes, $offset, $length));
    }

    /**
     * This methods peforms the actual operation of pushing back a string.
     *
     * @param string $bytes The string to be pushed back.
     *
     * @throws IOException If there is insufficient space in the internal
     * buffer.
     */
    protected function pushback($bytes)
    {
        if ($this->bufferSize < strlen($bytes.$this->buffer)) {

            throw new IOException(sprintf("Insufficient space in pushback buffer"));
        }

        $this->buffer = $bytes.$this->buffer;
    }
}
