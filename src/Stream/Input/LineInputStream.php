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
 * This subclass of filter input stream reads one line each time, from the
 * subordinate stream. Unlike the {@link fgets()} function, it does not have
 * limit on the length of the line.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class LineInputStream extends BufferableFilterInputStream
{
    /**
     * {@inheritdoc}
     */
    public function __construct(InputStreamInterface $in, $readBufferSize = 1024)
    {
        parent::__construct($in, $readBufferSize);
    }

    /**
     * Read a line from the input stream. A line is terminated by a NL or CR-NL
     * sequence.
     *
     * The line terminator is also returned as part of the returned string.
     * Returns null if no data is available.
     *
     * @return string The line read, or null if EOF.
     */
    public function readLine()
    {
        $line = null;

        while (1 !== ($matched = preg_match('/^([^\n]*\n)/', $this->buffer, $matches))) {

            if (-1 === $this->in->read($bytes, $this->readBufferSize)) {

                break;
            }

            $this->buffer .= $bytes;
        }

        if (1 === $matched) {

            $line = $matches[1];

            $this->buffer = substr($this->buffer, strlen($line));

        } else if (strlen($this->buffer) > 0) {

            $line = $this->buffer;

            $this->buffer = '';
        }

        return $line;
    }
}
