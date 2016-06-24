<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace ZerusTech\Component\IO\Stream\Output;

use ZerusTech\Component\IO\Stream\AbstractStream;
use ZerusTech\Component\IO\Stream\FlushableInterface;
use ZerusTech\Component\IO\Stream\Input\FileInputStream;
use ZerusTech\Component\IO\Exception\IOException;

/**
 * A file output stream is an output stream for writing data to a file.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 * @see FileInputStream
 */
class FileOutputStream extends AbstractStream implements OutputStreamInterface, FlushableInterface
{
    /**
     * @var string The path to the file to be opened for writing.
     */
    protected $source;

    /**
     * @var string The type of access to the opened file.
     */
    protected $mode;

    /**
     * Constructor.
     *
     * @param string $source The file path.
     * @param string $mode The access mode.
     */
    public function __construct($source, $mode)
    {
        $this->source = $source;

        $this->mode = $mode;

        $resource = @fopen($source, $mode);

        if (false === $resource) {

            $this->closed = true;

        } else {

            parent::__construct($resource);
        }
    }

    /**
     * Gets the file path.
     * @return string The file path.
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Gets the access mode.
     * @return string The access mode.
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if (true === $this->closed) {

            throw new IOException(sprintf("File %s is already closed, can't be closed again.", $this->source));
        }

        $this->flush();

        if (false === @fclose($this->resource)) {

            throw new IOException(sprintf("Failed to close %s.", $this->source));
        }

        $this->closed = true;

        $this->resource = null;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        if (true === $this->closed) {

            throw new IOException(sprintf("File %s is already closed, can't be flushed.", $this->source));
        }

        if (false === @fflush($this->resource)) {

            throw new IOException(sprintf("An unknown error occured when flushing file %s.", $this->source));
        }

        return $this;
    }

    /**
     * {@inehritdoc}
     */
    public function write($string)
    {

        if (true === $this->closed) {

            throw new IOException(sprintf("File %s is already closed, can't be written.", $this->source));
        }

        if (false === @fwrite($this->resource, $string)) {

            throw new IOException(sprintf("An unknown error occured when writing to file %s.", $this->source));
        }

        return $this;
    }
}
