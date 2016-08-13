<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace ZerusTech\Component\IO\Stream\Output;

use ZerusTech\Component\IO\Exception\IOException;
use ZerusTech\Component\IO\Stream\Output\OutputStreamInterface;
use ZerusTech\Component\IO\Stream\Input\PipedInputStreamInterface;

/**
 * A piped output stream can be connected to a piped input stream to create a
 * communication pipe. The piped output stream serves as the upstream and writes
 * data to the piped input stream.
 *
 * This class is not thread-safe, so it does not support wait and notify
 * features. The stream is never full, it can write data to the downstream at
 * any time.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class PipedOutputStream extends AbstractOutputStream implements PipedOutputStreamInterface
{
    /**
     * @var PipedInputStream The input stream to connect.
     */
    private $downstream;

    /**
     * Constructor.
     *
     * @param PipedInputStreamInterface $downstream The input stream to connect.
     */
    public function __construct(PipedInputStreamInterface $downstream = null)
    {
        $this->downstream = $downstream;

        $this->closed = false;

        if (null !== $this->downstream) {

            // Forces downstream to connect to current stream no matter it
            // is already connected or not and allows the downstream to call
            // the ``connect()`` method of current stream to complete the
            // connection.
            $this->downstream->connect($this, true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function connect(PipedInputStreamInterface $downstream, $force = false, $reverse = true)
    {
        if (false === $force && null !== $this->downstream && false === $this->closed) {

            throw new IOException(sprintf("Already connected."));
        }

        $this->downstream = $downstream;

        $this->closed = false;

        if (true === $reverse) {

            $downstream->connect($this, true, false);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function write($string)
    {
        if (true === $this->closed) {

            throw new IOException(sprintf("Can't write to a closed stream."));
        }

        if (null === $this->downstream) {

            throw new IOException(sprintf("Current stream is not connected to any downstream."));
        }

        if (null !== $string) {

            $this->downstream->receive($string);
        }

        return $this;
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

        return $this;
    }
}
