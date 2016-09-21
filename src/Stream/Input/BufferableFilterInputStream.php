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
 * This subclass of ``FilterInputStream`` provides an internal buffer to store
 * pre-fetched bytes from the subordinate input stream.
 *
 * This class does not implement the logic for pre-fetching bytes, so the
 * internal buffer is always empty in this class. However, all methods of this
 * class are aware of the existing of internal buffer, so they are supposed to
 * work when the internal buffer is not empty.
 *
 * Subclasses of this class are responsible for pre-fetching bytes into the
 * internal buffer.
 *
 * NOTE: this class does not support mark and reset, but subclasses of this
 * class may override the ``markSupport()`` method to support mark/reset
 * features.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class BufferableFilterInputStream extends FilterInputStream
{
    /**
     * @var string The internal buffer that stores pre-fetched bytes from the
     * subordinate stream.
     */
    protected $buffer;

    /**
     * @var int The maximum number of bytes to be read when pre-fetching bytes
     * from the subordinate input stream.
     */
    protected $readBufferSize;

    /**
     * Create a bufferable filter input stream instance with the specified
     * subordinate input stream.
     *
     * @param InputStreamInterface $in The subordinate input stream.
     * @param int $readBufferSize The maximum number of bytes to be pre-fetched.
     */
    public function __construct(InputStreamInterface $in, $readBufferSize = 1024)
    {
        parent::__construct($in);

        $this->buffer = '';

        $this->readBufferSize = $readBufferSize;
    }

    /**
     * {@inheritdoc}
     *
     * This method tries to read bytes from the internal buffer first. If the
     * internal buffer does not have enough bytes, additional bytes will be read
     * from the subordinate input stream.
     */
    protected function input(&$bytes, $length)
    {
        $remaining = $length;

        $count = min(strlen($this->buffer), $length);

        if ($count > 0) {

            $bytes = substr($this->buffer, 0, $count);

            $this->buffer = substr($this->buffer, $count);

            $remaining -= $count;
        }

        if ($remaining > 0 && -1 !== $count = $this->in->readSubstring($bytes, $count, $remaining)) {

            $remaining -= $count;
        }

        return -1 === $count && $remaining === $length ? -1 : $length - $remaining;
    }

    /**
     * {@inheritdoc}
     *
     * This method returns the total number of bytes availble in the internal
     * buffer and the subordinate input stream.
     */
    public function available()
    {
        return strlen($this->buffer) + $this->in->available();
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
        throw new IOException(sprintf("mark/reset not supported."));
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        parent::close();

        $this->buffer = '';

        return $this;
    }
}
