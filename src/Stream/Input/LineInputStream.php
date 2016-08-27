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
     * {@inheritdoc}
     */
    public function __construct(InputStreamInterface $in)
    {
        parent::__construct($in);

        $this->buffer = '';
    }

    /**
     * This method keeps reading a trunk of ``$length`` bytes each time, until a
     * line feed is found or EOF is reached. So parameter ``$length`` represents
     * the reading buffer size.
     *
     * Unlike its parent class, this method may read more bytes than ``$length``.
     *
     * The actual number of bytes read is returned as as int. A -1 is returned
     * to indicate the end of the stream.
     *
     * @param int $length The number of bytes it reads from the subordinate
     * stream each time, 1024 by default.
     */
    protected function input(&$bytes, $length = 1024)
    {
        $bytes = '';

        if (0 !== strlen($this->buffer)) {

            if (1 === preg_match('/^([^\n]*\n)/', $this->buffer, $matches)) {

                $bytes = $matches[1];

                $this->buffer = substr($this->buffer, strlen($bytes));

                return strlen($bytes);
            }
        }

        if (-1 === (parent::input($bytes, $length))) {

            $bytes = $this->buffer;

            return 0 === strlen($bytes) ? -1 : strlen($bytes);
        }

        $this->buffer .= $bytes;

        return $this->input($bytes, $length);
    }
}
