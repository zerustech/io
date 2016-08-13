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

/**
 * This class is the common superclass of output stream classes that filter the
 * output they write. These classes typically transform the data in some way
 * prior to writing it out to another underlying output stream. This class
 * simply overrides all the underlying stream. Subclasses privde actual
 * filtering.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class FilterOutputStream extends AbstractOutputStream
{
    /**
     * @var OutputStreamInterface This is the subordinate output stream that
     * this class redirects its method calls to.
     */
    protected $out;

    /**
     * This method creates an instance of filter output stream.
     *
     * @param OutputStreamInterface $out The output stream to write to.
     */
    public function __construct(OutputStreamInterface $out)
    {
        parent::__construct();

        $this->out = $out;
    }

    /**
     * {@inheritdoc}
     *
     * Redirects request to the subordinate output stream by calling
     * ``$this->out->close()``
     */
    public function close()
    {
        $this->flush();

        $this->out->close();

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * Redirects request to the subordinate output stream by calling
     * ``$this->out->flush()``
     */
    public function flush()
    {
        $this->out->flush();

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * Redirects request to the subordinate output stream by calling
     * ``$this->out->write()``
     */
    public function write($data)
    {
        $this->out->write($data);

        return $this;
    }
}
