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
use ZerusTech\Component\IO\Stream\Input\PipedInputStreamInterface;

/**
 * Stream classes that implemented this interface can connect to a piped input
 * stream and write data to it.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
interface PipedOutputStreamInterface
{
    /**
     * Connects current stream to a piped input stream.
     *
     * @param PipedInputStreamInterface $downstream The piped input stream to
     * connect.
     * @param bool $force Controls whether to override the existing connection
     * or not. Default to false.
     * @param bool $reverse Controls whether the ``connect()`` method of
     * ``$downstream`` should be called to setup reverse connection.
     * @return PipedOutputStreamInterface Current instance.
     * @throws IOException If ``$force`` is false and current stream
     * has already connected to a piped input stream.
     */
    public function connect(PipedInputStreamInterface $downstream, $force = false, $reverse = true);
}
