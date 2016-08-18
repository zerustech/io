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

use ZerusTech\Component\IO\Exception\IOException;

/**
 * This class allows data to be written to a string buffer and then retrieved by
 * an application. The internal string buffer is dynamically resized to hold
 * all the data written. Please be aware that writing large amounts of data to
 * this stream will cause large amounts of memory to be allocated.
 *
 * The purpose of this class is to provide an implementation that connects a
 * string with other streams to get fluent code.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class StringOutputStream extends AbstractOutputStream
{
    /**
     * @var string The internal buffer where the data written is stored.
     */
    private $buffer;

    /**
     * This method creates a new string output stream.
     */
    public function __construct()
    {
        $this->buffer = '';

        parent::__construct();
    }

    /**
     * This method discards all of the bytes that have been written to the
     * internal buffer so far by setting the ``$buffer`` variable to empty.
     * @return StringOutputStream Current stream.
     */
    public function reset()
    {
        $this->buffer = '';

        return $this;
    }

    /**
     * This method returns the length of the internal buffer.
     * @return int Length of the internal buffer.
     */
    public function size()
    {
        return strlen($this->buffer);
    }

    /**
     * {@inheritdoc}
     */
    protected function writeBytes($bytes)
    {
        if (true === $this->closed) {

            throw new IOException(sprintf("Stream is already closed, can't be written."));
        }

        $this->buffer .= $bytes;

        return $this;
    }

    /**
     * This method writes all the bytes that have been written to this stream
     * from the internal buffer to the specified output stream.
     * @param OutputStreamInterface $out The output stream to write to.
     * @return StringOutputStream Current stream.
     * @throws IOException If error occurs.
     */
    public function writeTo(OutputStreamInterface $out)
    {
        $out->write($this->buffer);

        return $this;
    }

    /**
     * Returns the bytes in the internal buffer as a string.
     * @return string The string that represents the data of the internal buffer.
     */
    public function __toString()
    {
        return $this->buffer;
    }
}
