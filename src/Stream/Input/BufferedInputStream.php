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
 * This subclass of filter input stream buffers input from an underlying
 * implementation to provide a possibly more efficient read mechanism.
 *
 * It maintains the buffer and buffer state in instance variables that are
 * available to subclasses. The default buffer size of 1024 bytes can be
 * overridden by the creator of the stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class BufferedInputStream extends FilterInputStream
{
    /**
     * @var string The buffer used for storing data from the underlying stream.
     */
    private $buffer;

    /**
     * @var int This is the initial buffer size. When the buffer is grown
     * because of marking requirements, it will be grown by buffer size
     * increments. The underlying stream will be read in chunks of buffer size.
     */
    private $bufferSize = 1024;

    /**
     * @var int The index of the next byte that will be read from the buffer.
     */
    private $offset;

    /**
     * @var int The number of valid bytes currently in the buffer. It is also
     * the index of the buffer position one byte past the end of the valid data.
     */
    private $count;

    /**
     * @var int The value of ``$this->offset`` when ``mark()`` method was
     * called. This is set to -1 if there is no mark set.
     */
    private $mark;

    /**
     * @var int This is the maximum number of bytes that can be read after a
     * call to ``mark()`` method before the mark can be discarded.
     */
    private $markLimit;

    /**
     * This method initializes a new buffered input stream that will read from
     * the specified subordinate stream with a buffer size that is specified by
     * the caller.
     *
     * @param InputStreamInterface $in The subordinate stream to read from.
     * @param int $bufferSize The buffer size to use.
     *
     * @throws \InvalidArgumentException When the buffer size is smaller than 1.
     */
    public function __construct(InputStreamInterface $in, $bufferSize = 1024)
    {
        parent::__construct($in);

        if ($bufferSize <= 0) {

            throw new \InvalidArgumentException(sprintf("The buffer size must be greater than %d.", 0));
        }

        $this->bufferSize = $bufferSize;

        $this->buffer = '';

        $this->offset = 0;

        $this->count = 0;

        $this->mark = -1;

        $this->markLimit = 0;
    }

    /**
     * {@inheritdoc}
     */
    protected function input(&$bytes, $length)
    {
        $bytes = '';

        $remaining = $length;

        while ($remaining > 0) {

            $numberOfBytes = min($remaining, ($this->count - $this->offset));

            if ($numberOfBytes > 0) {

                // Tries to read bytes, if any, from current buffer first.

                $bytes .= substr($this->buffer, $this->offset, $numberOfBytes);

                $this->offset += $numberOfBytes;

                $remaining -= $numberOfBytes;
            }

            if ($this->offset === $this->count && false === $this->fillBuffer()) {

                // If current buffer has become empty, tries to read another
                // chunks of bytes, up to buffer size, from the underlying
                // stream.
                //
                // Breaks the loop if there is no byte available (at EOF).

                break;
            }
        }

        $count = $length - $remaining;

        return 0 === $count && $length > 0 ? -1 : $count;
    }

    /**
     * {@inheritdoc}
     */
    public function available()
    {
        return $this->count - $this->offset + parent::available();
    }

    /**
     * {@inheritdoc}
     */
    public function mark($limit)
    {
        $this->markLimit = $limit;

        $this->mark = $this->offset;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function markSupported()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        if (-1 === $this->mark) {

            throw new IOException(sprintf("%s", $this->isClosed() ? "Stream closed." : "Invalid mark."));
        }

        $this->offset = $this->mark;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        parent::close();

        $this->mark = -1;

        $this->buffer = '';

        $this->count = 0;

        $this->offset = 0;

        return $this;
    }

    /**
     * Called to refill the buffer (when count is equal to offset).
     *
     * @return bool True when at least one additional byte was read into buffer,
     * false otherwise (at EOF).
     */
    private function fillBuffer()
    {
        if (-1 === $this->mark || ($this->offset - $this->mark) >= $this->markLimit) {

            //No mark was set, or mark has become invalid.

            $this->buffer = '';

            $this->offset = $this->count = 0;

            $this->mark = -1;

        } else if ($this->mark > 0) {

            // Mark was set and it is still valid, so must keep all the bytes
            // after mark, new bytes should be appended to current buffer

            $this->buffer = substr($this->buffer, $this->mark);

            $this->offset -= $this->mark;

            $this->count -= $this->mark;

            $this->mark = 0;
        }

        $count = max(0, $this->in->input($bytes, $this->bufferSize));

        $this->buffer .= $bytes;

        $this->count += $count;

        return  $count > 0;
    }
}
