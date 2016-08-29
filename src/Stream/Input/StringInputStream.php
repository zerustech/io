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
     * Constructor.
     *
     * @param string $buffer The underlying input buffer.
     */
    public function __construct($buffer)
    {
        parent::__construct();

        $this->buffer = $buffer;
    }

    /**
     * {@inheritdoc}
     */
    protected function input(&$bytes, $length)
    {
        $bytes = substr($this->buffer, $this->position, min($length, strlen($this->buffer) - $this->position));

        $count = strlen($bytes);

        $this->position += $count;

        return 0 === $count ? -1 : $count;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        parent::close();

        $this->buffer = null;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function available()
    {
        return strlen($this->buffer) - $this->position;
    }
}
