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
}
