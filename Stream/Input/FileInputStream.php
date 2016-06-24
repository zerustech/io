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
use ZerusTech\Component\IO\Stream\AbstractStream;

/**
 * A file input stream obtains input bytes from a file.
 *
 * The file can be a physical file on file system or virtual file (for example,
 * ``'php://stdin'``). What files are available depends on the host environment.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class FileInputStream extends AbstractStream implements InputStreamInterface
{
    /**
     * @var string The path to the file to be opened for reading.
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
     * @param string $mode The accessing mode.
     */
    public function __construct($source, $mode)
    {
        $this->source = $source;

        $this->mode = $mode;

        $resource = @fopen($source, $mode);

        parent::__construct($resource);
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
    public function read($length = 1)
    {
        $data = '';

        if (true !== $this->closed) {

            $data = @fread($this->resource, $length);
        }

        if (false === $data) {

            throw new IOException(sprintf("An unknown error occured when reading data from file %s.", $this->source));
        }

        return $data;
    }
}
