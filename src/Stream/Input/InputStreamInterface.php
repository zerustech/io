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
 * This is the interface for all input stream classes.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
interface InputStreamInterface
{
    /**
     * This method reads up to ``$length`` bytes from a stream and stores them
     * into the caller supplied buffer. The actual number of bytes read is
     * returned as an int. A -1 is returned to indicate the end of the stream.
     *
     * This method operates by calling ``readSubstring()`` method like so:
     * ``$this->readSubstring($bytes, 0, $length);``
     *
     * @param string $bytes The buffer into which the bytes read will be stored.
     * @param int $length The requested number of bytes to read, 1 by default.
     * @return int The actual number of bytes read or -1 if end of stream.
     */
    public function read(&$bytes, $length = 1);

    /**
     * This method read bytes from a stream and stores them into a caller
     * supplied buffer. It starts storing the data at index ``$offset`` into the
     * buffer and attempts to read ``$length`` bytes. The actual number of bytes
     * read is returned as an int. A -1 is returned to indicate the end of the
     * stream.
     *
     * Firstly:
     *
     * If ``$offset >= 0``, the bytes read start to store at offset'th position
     * in the string, counting from zero, left to right.
     *
     * If ``$offset < 0``, ``$offset = max(0, strlen($bytes) + $offset)``.
     *
     * If ``$length > 0``, up to ``$length`` bytes will be read and stored in
     * the buffer.
     *
     * If ``$length < 0``,
     * ``$length = max(0, strlen($bytes) - $offset + $length)``.
     *
     * Finally:
     *
     * If ``$offset > strlen($bytes)``, an out of bounds exception is thrown.
     *
     * If ``$length`` is 0, null or false, an out of bounds exception is thrown.
     *
     * If ``strlen($bytes) === 0 and $offset === 0``, '' is read.
     *
     * So basically, the logic is as follows:
     *
     *     $offset = 0 > $offset ? max(0, strlen($bytes) + $offset) : $offset;``
     *
     *     $length = 0 > $length ? max(0, strlen($bytes) - $offset + $length) : $length;``
     *
     *     if ($offset > strlen($bytes) || 0 === $length || null === $length || false === $length) {
     *
     *         throw new \OutOfBoundsException(...);
     *     }
     *
     * NOTE: the exception criteria for input stream interface is a bit
     * different with it for the output stream interface:
     * - For ``$offset``, the criteria is ``$offset > strlen($bytes)``
     * - For ``$length``, the criteria is
     * ``0 === $length || null === $length || false === $length``
     *
     * @param string $bytes The buffer into which the bytes read should be
     * stored.
     * @param int $offset The offset into the buffer to start storing bytes.
     * @param int $length The requested number of bytes to read.
     * @return int The actual number of bytes read, or -1 if end of stream.
     */
    public function readSubstring(&$bytes, $offset, $length);

    /**
     * This method returns the number of bytes that can be read from this stream
     * before a read can block. A return of 0 indicates that blocking might (or
     * might not) occur on the very next read attempt.
     *
     * @return int The number of bytes that can be read before blocking could
     * occur.
     * @throws IOException If this stream is closed or an error occurs.
     */
    public function available();

    /**
     * This method marks a position in the input to which the stream can be
     * "reset" by calling the ``reset()`` method. The parameter ``$readLimit``
     * is the number of bytes that can be read from the stream after setting the
     * mark before the mark becomes invalid.
     *
     * For example, if ``mark()`` is called with a read limit of 10, then when
     * 11 bytes of data are read from the stream before the ``reset()`` method
     * is called, then the mark is invalid and the stream object instance is not
     * required to remember the mark.
     *
     * @param int $limit The number of bytes that can be read from this
     * stream before the mark becomes invalid.
     * @see reset()
     */
    public function mark($limit);

    /**
     * This method returns a boolean that indicates whether the mark/reset
     * methods are supported in this class. Those methods can be used to
     * remember a specific point in the stream and reset the stream to that
     * point.
     *
     * @return bool True if mark/reset functionality is supported, false
     * otherwise.
     */
    public function markSupported();

    /**
     * This method resets a stream to the point where the ``mark`` method was
     * called. Any bytes that were read after the mark point was set will be
     * re-read during subsequent read.
     *
     * @throws IOException If mark/reset is not supported, the ``mark()`` method
     * was never called, or more than mark limit bytes were read since the last
     * call to ``mark()`` method.
     */
    public function reset();

    /**
     * This method skips the specified number of bytes in the stream. It retruns
     * the actual number of bytes skipped, which may be less than the requred
     * amount.
     *
     * @param int $length The requested number of bytes to skip.
     * @param int $buffer The maximum number of bytes to be read each time.
     * @return int The actual number of bytes skipped.
     * @throws IOException If an error occurs.
     */
    public function skip($length, $buffer = 1024);
}
