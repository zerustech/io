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

/**
 * This abstract class is the superclass of all classes representing an input or
 * output stream.
 *
 * A stream is a source (input stream) or destination (output stream) of data.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
abstract class AbstractStream implements ClosableInterface
{
    /**
     * @var resource The resource being held by current stream.
     */
    protected $resource;

    /**
     * @var bool True if current resource is closed, and false otherwise.
     */
    protected $closed;

    /**
     * Constructor.
     *
     * @param resource $resource The underlying resource.
     */
    public function __construct($resource)
    {
        $this->resource = $resource;

        $this->closed = false === $this->resource ? true : false;
    }

    /**
     * Gets the underlying resource.
     * @return resource The underlying resource.
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Checks whether current stream is closed or not.
     * @return bool True if current stream is closed, and false otherwise.
     */
    public function isClosed()
    {
        return $this->closed;
    }
}
