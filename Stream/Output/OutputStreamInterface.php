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
     * Writes the given string into the underline resource.
     *
     * @param string The string to be written to the resource.
     * @return AbstractOutputStream Current instance.
     * @throws IOException If an I/O error occurs.
     */
    public function write($string);
}
