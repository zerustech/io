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
 * A file input stream obtains input bytes from a file.
 *
 * The file can be a physical file on file system or virtual file (for example,
 * ``'php://stdin'``). What files are available depends on the host environment.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class FileInputStream extends AbstractInputStream
{
    /**
     * @var string The path to the file to be opened for reading.
     */
    private $source;

    /**
     * @var string The type of access to the opened file.
     */
    private $mode;

    /**
     * @var resource The resource that represents the underlying file.
     */
    private $resource;

    /**
     * Create a new file input stream instance.
     *
     * @param string $source The file path.
     * @param string $mode The accessing mode.
     */
    public function __construct($source, $mode)
    {
        parent::__construct();

        $this->source = $source;

        $this->mode = $mode;

        $this->resource = @fopen($source, $mode);

        $this->closed = false === $this->resource ? true : false;
    }

    /**
     * Gets the file path.
     *
     * @return string The file path.
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Gets the access mode.
     *
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

        $this->position = 0;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function input(&$bytes, $length)
    {
        $bytes = @fread($this->resource, $length);

        if (false === $bytes && $length > 0) {

            throw new IOException(sprintf("An unknown error occured when reading data from file %s.", $this->source));
        }

        $count = strlen($bytes);

        $this->position += $count;

        return $length > 0 && 0 === $count ? -1 : $count;
    }

    /**
     * {@inheritdoc}
     */
    public function available()
    {
        return filesize($this->source) - $this->position;
    }
}
