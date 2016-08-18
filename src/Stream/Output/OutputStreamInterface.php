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
 * This is the interface for all output stream classes.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
interface OutputStreamInterface
{
    /**
     * This method writes ``$length`` bytes from the specified string ``$bytes``
     * starting at index ``$offset`` into the stream.
     *
     * @param string $bytes The string of bytes to write from.
     * @param int $offset The index in the string to start writing from, 0 by
     * default.
     * @param int|null $length The number of bytes to write, or null to write
     * all remaining bytes from index ``$offset`` to the end of the string.
     * @return OutputStreamInterface Current instance.
     * @throws IOException If an I/O error occurs.
     * @throws \OutOfBoundsException If ``$offset`` or ``$length`` is out of
     * bounds.
     */
    public function write($bytes, $offset = 0, $length = null);
}
