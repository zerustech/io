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
 * This subclass of ``FilterInputStream`` provides the ability to unread data
 * from a stream. If maintains an internal buffer of unread data that is
 * supplied to the next read operation. This is conceptually similar to
 * mark/reset functionality, except that in this case the position to reset the
 * stream to does not need to be known
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class PushbackInputStream extends FilterInputStream
{
    /**
     * @var InputStreamInterface This is the subordinate input stream to which
     * method calls are redirected.
     */
    private $bufferSize = 1;

    /**
     * @var string The internal buffer to store bytes that are pushed back.
     */
    protected $buffer = null;

    /**
     * Create a filter input stream instance with the specified subordinate
     * input stream.
     *
     * @param InputStreamInterface $in The subordinate input stream.
     */
    public function __construct(InputStreamInterface $in, $bufferSize = 1)
    {
        parent::__construct();

        $this->in = $in;

        $this->bufferSize = $bufferSize;

        $this->buffer = '';
    }

    /**
     * {@inheritdoc}
     */
    public function available()
    {
        return strlen($this->buffer) + parent::available();
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
    public function close()
    {
        parent::close();

        $this->buffer = null;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function input(&$bytes, $length)
    {
        $remaining = $length;

        $count = min(strlen($this->buffer), $length);

        if ($count > 0) {

            $bytes .= substr($this->buffer, 0, $count);

            $remaining -= $count;
        }

        if ($remaining > 0 && -1 !== $count = $this->in->read($bytes, strlen($bytes), $remaining)) {

            $remaining -= $count;
        }

        return -1 === $count && $remaining === $length ? -1 : $length - $remaining;
    }

    public function unread($bytes)
    {
        return $this->unreadSubstring($bytes, 0, strlen($bytes));
    }

    public function unreadSubstring($bytes, $offset, $length)
    {
        $offset = $offset < 0 ? max(0, strlen($bytes) + $offset) : $offset;

        $length = $length < 0 ? max(0, strlen($bytes) - $offset + $length) : $length;

        if (strlen($bytes) > 0 && $offset > strlen($bytes) || null === $length || false === $length) {

            throw new \OutOfBoundsException(sprintf("Invalid offset or length."));
        }

        if (true === $this->closed) {

            throw new IOException(sprintf("Stream is already closed, can't be read."));
        }

        return $this->pushback(substr($bytes, $offset, $length));
    }

    protected function pushback($bytes)
    {
        if ($this->bufferSize < strlen($bytes.$this->buffer)) {

            throw new IOException(sprintf("Insufficient space in pushback buffer"));
        }

        $this->buffer = $bytes.$this->buffer;
    }
}
