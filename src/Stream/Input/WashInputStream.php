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

use ZerusTech\Component\IO\Stream\Input\InputStreamInterface;
use ZerusTech\Component\IO\Stream\Input\FilterInputStream;

/**
 * This class reads bytes from the subordinate input stream and
 * cleans up specifal characters (by default, "\n", "\r", "\t" and " ") from them.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class WashInputStream extends FilterInputStream
{
    /**
     * @var string The regex pattern for searching characters to be washed.
     */
    private $searchPattern;

    /**
     * @var string The regex pattern for replacing the matched characters.
     */
    private $replacePattern;

    /**
     * This method creates a new ascii hexadecimal wash input stream.
     *
     * @param InputStreamInterface $in The subordinate input stream.
     */
    public function __construct(InputStreamInterface $in, $searchPattern = "/([^\n\t\r ]*)([\n\t\r ]*)([^\n\r\t ]*)/", $replacePattern = "$1$3")
    {
        parent::__construct($in);

        $this->searchPattern = $searchPattern;

        $this->replacePattern = $replacePattern;
    }

    /**
     * {@inheritdoc}
     *
     * This methods returns 1 if the subordinate input stream is still
     * available, or 0 otherwise.
     *
     * @return int 1 if the subordinate input stream is till available, or 0
     * otherwise.
     */
    public function available()
    {
        return parent::available() > 0 ? 1 : 0;
    }

    /**
     * {@inheritdoc}
     *
     * This method keeps reading bytes from the subordinate input
     * stream and cleaning up space characters from them, untill ``$length``
     * clean bytes have been generated, or EOF has been reached.
     *
     * @return int The number of hexadecimal bytes converted, or -1 if EOF.
     */
    protected function input(&$bytes, $length)
    {
        $remaining = $length;

        $bytes = '';

        while ($remaining > 0 && -1 !== parent::input($hex, $remaining)) {

            $hex = preg_replace($this->searchPattern, $this->replacePattern, $hex);

            $bytes .= $hex;

            $remaining -= strlen($hex);
        }

        return $remaining === $length ? -1 : $length - $remaining;
    }
}
