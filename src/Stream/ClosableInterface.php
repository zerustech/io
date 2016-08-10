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
 * A Closeable is a source or destination of data that can be closed.
 *
 * The close method is invoked to release resources that the object is holding
 * (such as open files).
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
interface ClosableInterface
{
    /**
     * Releases the resoruces that is being held.
     *
     * @return ClosableInterface Current instance.
     * @throws IOException If an I/O error occurs.
     */
    public function close();

    /**
     * Checks if current resource has been closed.
     * @return bool True if current resource has been closed, and false
     * otherwise.
     */
    public function isClosed();
}
