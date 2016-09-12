<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with the source code.
 */

namespace ZerusTech\Component\IO\Stream\Output;

use ZerusTech\Component\IO\Stream\Output\FilterOutputStream;
use ZerusTech\Component\IO\Stream\Output\OutputStreamInterface;

/**
 * This class removes special characters, (by default, "\n", "\r", "\t" and " ")
 * from the provided bytes and writes the bytes to the subordinate output stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class WashOutputStream extends FilterOutputStream
{

    /**
     * @var string The regex pattern for searching characters to be removed.
     */
    private $searchPattern;

    /**
     * @var string The regex for replacing the matched characters.
     */
    private $replacePattern;

    /**
     * This method creates a new wash output stream.
     *
     * @param OutputStreamInterface $out The subordinate output stream.
     */
    public function __construct(OutputStreamInterface $out, $searchPattern = "/([^\n\t\r ]*)([\n\t\r ]*)([^\n\r\t ]*)/", $replacePattern = "$1$3")
    {
        parent::__construct($out);

        $this->searchPattern = $searchPattern;

        $this->replacePattern = $replacePattern;
    }

    /**
     * {@inheritdoc}
     *
     * This method removes space characters from ``$bytes`` and writes the
     * washed string to the subordinate stream.
     */
    protected function output($bytes)
    {
        return parent::output(preg_replace($this->searchPattern, $this->replacePattern, $bytes));
    }
}
