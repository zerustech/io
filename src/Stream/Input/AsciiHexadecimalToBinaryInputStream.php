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

@trigger_error('The '.__NAMESPACE__.'\AsciiHexadecimalToBinaryInputStream class is deprecated since version 2.0.1 and will be removed in 3.0. Use the same class in zerustech/postscript package instead.', E_USER_DEPRECATED);

use ZerusTech\Component\Postscript\Font\TypeOne\Stream\Input\AsciiHexadecimalToBinaryInputStream as PostscriptAsciiHexadecimalToBinaryInputStream;

/**
 * This class converts the data read from the subordinate input stream from
 * ascii hexadecimal format to binary format.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 * @deprecated since 2.0.1, to be removed in 3.0. Use the same class in
 * zerustech/postscript package instead.
 */
class AsciiHexadecimalToBinaryInputStream extends PostscriptAsciiHexadecimalToBinaryInputStream
{
}
