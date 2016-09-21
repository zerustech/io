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
 * This class allows an application to create an input stream in which the bytes
 * are supplied by the contents of a string.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class StringInputStream extends AbstractInputStream
{
    /**
     * @var string The string from which bytes are read.
     */
    private $buffer;

    /**
     * @var int The index, in the string, of the next byte to read.
     */
    private $offset;

    /**
     * Constructor.
     *
     * @param string $buffer The underlying input buffer.
     */
    public function __construct($buffer)
    {
        parent::__construct();

        $this->buffer = $buffer;

        $this->offset = 0;
    }

    /**
     * {@inheritdoc}
     */
    protected function input(&$bytes, $length)
    {
        $bytes = substr($this->buffer, $this->offset, min($length, strlen($this->buffer) - $this->offset));

        $count = strlen($bytes);

        $this->offset += $count;

        return 0 === $count ? -1 : $count;
    }

    /**
     * {@inheritdoc}
     */
    public function available()
    {
        return strlen($this->buffer) - $this->offset;
    }
}
