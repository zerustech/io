<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace ZerusTech\Component\IO\Stream;

use ZerusTech\Component\IO\Exception\IOException;

/**
 * A flushable is a destination of data that can be flushed.
 *
 * The flush method is invoked to write any buffered output to the underlying
 * stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
interface FlushableInterface
{
    /**
     * Flushes any buffered output to the underlying stream.
     *
     * @return FlushableInterface Current instance.
     * @throws IOException if an I/O error occurs.
     */
    public function flush();
}
