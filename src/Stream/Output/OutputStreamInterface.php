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
     * This method writes all bytes from the specified string ``$bytes`` into
     * the stream.
     *
     * This method is equivalent to ``writeSubstring($bytes, 0, strlen($bytes))``
     * which is exactly how it is implemented in this class.
     *
     * @param string $bytes The string to write from.
     * @return int The actual number of bytes written to the stream.
     * @throws IOException If an I/O error occurs.
     */
    public function write($bytes);

    /**
     * This method writes ``$length`` bytes from the specified string ``$bytes``
     * starting at index ``$offset`` into the stream.
     *
     * Firstly:
     *
     * If ``$offset >= 0``, the writing starts at offset'th position in the string,
     * counting from zero, left to right.
     *
     * If ``$offset < 0``, ``$offset = max(0, strlen($bytes) + $offset)``.
     *
     * If ``$length > 0``, the writing will write up to ``$length`` bytes from
     * the index of ``$offset``.
     *
     * If ``$length < 0``,
     * ``$length = max(0, strlen($bytes) - $offset + $length)``.
     *
     * Finally:
     *
     * If ``$offset > 0 && $offset >= strlen($bytes)``, an out of bounds exception is thrown. ($bytes is not '')
     *
     * If ``$length`` is null or false, an out of bounds exception is thrown.
     *
     * If ``$length === 0``, '' is written.
     *
     * If ``strlen($bytes) === 0 and $offset === 0``, '' is written.
     *
     * So basically, the logic is as follows:
     *
     *     $offset = 0 > $offset ? max(0, strlen($bytes) + $offset) : $offset;``
     *
     *     $length = 0 > $length ? max(0, strlen($bytes) - $offset + $length) : $length;``
     *
     *     if ($offset > 0 && $offset >= strlen($bytes) || null === $length || false === $length) {
     *
     *         throw new \OutOfBoundsException(...);
     *     }
     *
     * @param string $bytes The string of bytes to write from.
     * @param int $offset The index in the string to start writing from.
     * @param int $length The number of bytes to write.
     * @return int The actual number of bytes written to the stream.
     * @throws IOException If an I/O error occurs.
     * @throws \OutOfBoundsException If ``$offset`` or ``$length`` is out of
     * bounds.
     */
    public function writeSubstring($bytes, $offset, $length);
}
