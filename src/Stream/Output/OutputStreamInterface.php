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
     * @param string $data The string to be written to the resource.
     * @return AbstractOutputStream Current instance.
     * @throws IOException If an I/O error occurs.
     * @deprecated This method is deprecated as of 1.0.6 and will be redefined
     * in 2.0.
     */
    public function write($data);
}
