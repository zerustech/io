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
use ZerusTech\Component\IO\Stream\Output\PipedOutputStreamInterface;

/**
 * Stream classes that implemented this interface can connect to a piped output
 * stream and receive data from it.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 * @see PipedOutputStreamInterface
 */
interface PipedInputStreamInterface
{
    /**
     * Connects current stream to a piped output stream.
     *
     * @param PipedOutputStreamInterface $upstream The piped output stream to
     * connect.
     * @param bool $force Controls whether to override the existing connection
     * or not. Default to false.
     * @param bool $reverse Controls whether the ``connect()`` method of
     * ``$upstream`` should be called to setup reverse connection.
     * @return PipedInputStreamInterface Current instance.
     * @thorws IOException If ``$force`` is false and current stream has already
     * connected to a piped output stream.
     */
    public function connect(PipedOutputStreamInterface $upstream, $force = false, $reverse = true);

    /**
     * Receives the given string from the upstream and the received string will
     * be stored in the internal buffer.
     *
     * @param string $string The string to receive.
     * @return PipedInputStreamInterface Current instance.
     */
    public function receive($string);
}
