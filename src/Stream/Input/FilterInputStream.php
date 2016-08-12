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
 * This is the common superclass of all standard classes that filter input. It
 * acts as a layer on top of an underlying input stream and simply redirects
 * calls made to it to the subordinate input stream instead.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class FilterInputStream extends AbstractInputStream
{
    /**
     * @var InputStreamInterface This is the subordinate input stream to which
     * method calls are redirected.
     */
    protected $in;

    /**
     * Create a filter input stream instance with the specified subordinate
     * input stream.
     *
     * @param InputStreamInterface $in The subordinate input stream.
     */
    public function __construct(InputStreamInterface $in)
    {
        $this->in = $in;
    }

    /**
     * {@inheritdoc}
     *
     * Redirects request to the subordinate input stream by calling
     * ``$this->in->read()``
     */
    public function read($length = 1)
    {
        return $this->in->read($length);
    }

    /**
     * {@inheritdoc}
     *
     * Redirects request to the subordinate input stream by calling
     * ``$this->in->available()``
     */
    public function available()
    {
        return $this->in->available();
    }

    /**
     * {@inheritdoc}
     *
     * Redirects request to the subordinate input stream by calling
     * ``$this->in->mark()``
     */
    public function mark($readLimit)
    {
        return $this->in->mark($readLimit);
    }

    /**
     * {@inheritdoc}
     *
     * Redirects request to the subordinate input stream by calling
     * ``$this->in->markSupported()``
     */
    public function markSupported()
    {
        return $this->in->markSupported();
    }

    /**
     * {@inheritdoc}
     *
     * Redirects request to the subordinate input stream by calling
     * ``$this->in->reset()``
     */
    public function reset()
    {
        return $this->in->reset();
    }

    /**
     * {@inheritdoc}
     *
     * Redirects request to the subordinate input stream by calling
     * ``$this->in->skip()``
     */
    public function skip($byteCount)
    {
        return $this->in->skip($byteCount);
    }

    /**
     * Redirects request to the subordinate input stream by calling
     * ``$this->in->getResource()``
     *
     * @deprecated This method is deprecated as of 1.1.5 and will be removed in
     * 2.0. There is no alternative for it.
     */
    public function getResource()
    {
        @trigger_error(sprintf('%s is deprecated as of 1.1.5 and will be removed in 2.0. There is no alternative for it.', __METHOD__), E_USER_DEPRECATED);

        return $this->in->getResource();
    }

    /**
     * {@inheritdoc}
     *
     * Redirects request to the subordinate input stream by calling
     * ``$this->in->isClosed()``
     */
    public function isClosed()
    {
        return $this->in->isClosed();
    }

    /**
     * {@inheritdoc}
     *
     * Redirects request to the subordinate input stream by calling
     * ``$this->in->close()``
     */
    public function close()
    {
        return $this->in->close();
    }
}
