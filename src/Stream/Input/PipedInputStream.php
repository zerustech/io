<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace ZerusTech\Component\IO\Stream\Input;

use ZerusTech\Component\IO\Exception\IOException;
use ZerusTech\Component\IO\Stream\Output\PipedOutputStream;
use ZerusTech\Component\IO\Stream\Output\PipedOutputStreamInterface;

/**
 * A piped input stream can be connected to a piped output stream to create a
 * communications pipe. The piped input stream serves as the downstream and
 * receives data from the piped output stream.
 *
 * This class is not thread-safe, so it does not support the ``wait`` and
 * ``notify`` features. When no bytes are available in the stream, the
 * read method does not wait for bytes from the upstream and terminates
 * right away.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 * @see PipedOutputStream
 */
class PipedInputStream extends AbstractInputStream implements PipedInputStreamInterface
{
    /**
     * @var PipedOutputStreamInterface The output stream to connect.
     */
    private $upstream;

    /**
     * @var string The queue shared by the input stream and the output
     * stream.
     */
    private $buffer;

    /**
     * Constructor.
     *
     * @param PipedOutputStreamInterface $upstream The piped output stream to
     * connect.
     */
    public function __construct(PipedOutputStreamInterface $upstream = null)
    {
        parent::__construct();

        $this->buffer = '';

        $this->upstream = $upstream;

        $this->closed = false;

        if (null !== $upstream) {

            $this->connect($upstream, true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function connect(PipedOutputStreamInterface $upstream, $force = false, $reverse = true)
    {
        if (false === $force && null !== $this->upstream && false === $this->closed) {

            throw new IOException(sprintf("Already connected."));
        }

        $this->upstream = $upstream;

        $this->closed = false;

        if (true === $reverse) {

            $this->upstream->connect($this, true, false);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function input(&$bytes, $length)
    {
        $count = min($length, strlen($this->buffer));

        $bytes = substr($this->buffer, 0, $count).'';

        $this->buffer = substr($this->buffer, $count).'';

        $this->position += $count;

        return 0 === $count ? -1 : $count;
    }

    /**
     * {@inheritdoc}
     */
    public function receive($string)
    {
        $this->buffer .= $string;

        return strlen($string);
    }

    /**
     * {@inherit}
     */
    public function available()
    {
        return strlen($this->buffer);
    }
}
