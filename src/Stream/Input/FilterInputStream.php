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
        parent::__construct();

        $this->in = $in;
    }

    /**
     * {@inheritdoc}
     */
    protected function input(&$bytes, $length)
    {
        return $this->in->input($bytes, $length);
    }

    /**
     * {@inheritdoc}
     */
    public function available()
    {
        return $this->in->available();
    }

    /**
     * {@inheritdoc}
     */
    public function mark($limit)
    {
        $this->in->mark($limit);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function markSupported()
    {
        return $this->in->markSupported();
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->in->reset();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $this->in->close();

        parent::close();

        return $this;
    }
}
