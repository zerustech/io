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
 * This class allows an application to create an input stream in which the bytes
 * are supplied by the contents of a string.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class StringInputStream extends AbstractInputStream
{
    /**
     * @var string The string from which bytes are read.
     */
    private $buffer;

    /**
     * @var int The index of the next byte that will be read from the buffer.
     */
    private $position;

    /**
     * Constructor.
     *
     * @param string $buffer The underlying input buffer.
     */
    public function __construct($buffer)
    {
        parent::__construct();

        $this->buffer = $buffer;

        $this->position = 0;
    }

    /**
     * Gets the buffer.
     * @return string The buffer.
     */
    public function getBuffer()
    {
        return $this->buffer;
    }

    /**
     * Gets the index of the next byte to read.
     * @return int The index.
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length = 1)
    {
        $data = '';

        if (false === $this->closed && $length > 0) {

            $bytes = min($length, strlen($this->buffer) - $this->position);

            if ($bytes > 0) {

                $data = substr($this->buffer, $this->position, $bytes);

                $this->position += $bytes;
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if (true === $this->closed) {

            throw new IOException(sprintf("Already closed."));
        }

        $this->closed = true;

        $this->position = 0;

        $this->buffer = null;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function available()
    {
        return strlen($this->buffer) - $this->position;
    }
}
