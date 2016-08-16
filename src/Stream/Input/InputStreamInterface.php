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
     * Reads up to ``$length`` bytes from the input stream and return the data
     * as a string.
     *
     * @param int $length The maximum bytes to read.
     * @return string The string consists of the bytes read.
     * @throws IOException If an I/O error occurs.
     */
    public function read($length = 1);

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
     * @param int $readLimit The number of bytes that can be read from this
     * stream before the mark becomes invalid.
     * @see reset()
     */
    public function mark($readLimit);

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
     * @param int $byteCount The requested number of bytes to skip.
     * @return int The actual number of bytes skipped.
     * @throws IOException If an error occurs.
     */
    public function skip($byteCount);

    /**
     * This method returns the global offset, in bytes, in the underlying
     * resource / stream.
     * @return int The global offset.
     */
    public function offset();
}
