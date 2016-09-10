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
class LineInputStream extends FilterInputStream
{
    /**
     * @var string The buffer used for storing bytes read from the subordinate
     * stream before a line feed or EOF is reached.
     */
    private $buffer;

    /**
     * @var int The length of buffer.
     */
    private $bufferSize = 32;

    /**
     * {@inheritdoc}
     */
    public function __construct(InputStreamInterface $in, $bufferSize = 32)
    {
        parent::__construct($in);

        $this->buffer = '';

        $this->bufferSize = $bufferSize;
    }

    /**
     * {@inheritdoc}
     */
    public function available()
    {
        return strlen($this->buffer) + parent::available();
    }

    /**
     * This method keeps reading bytes from current input stream, untill a
     * line feed or EOF is reached. The byes read, including the line feed, is
     * returned.
     *
     * @return string The line read, or null if EOF.
     */
    public function readLine()
    {
        $line = null;

        while (1 !== ($matched = preg_match('/^([^\n]*\n)/m', $this->buffer, $matches))) {

            if (-1 === parent::read($buffer, $this->bufferSize)) {

                break;
            }

            $this->buffer .= $buffer;
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
