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

@trigger_error('The '.__NAMESPACE__.'\BinaryToAsciiHexadecimalOutputStream class is deprecated since version 2.0.1 and will be removed in 3.0. Use the same class in zerustech/postscript package instead.', E_USER_DEPRECATED);

use ZerusTech\Component\Postscript\Font\TypeOne\Stream\Output\BinaryToAsciiHexadecimalOutputStream as PostscriptBinaryToAsciiHexadecimalOutputStream;

/**
 * This class converts binary data to ascii hexadecimal prior to writing the
 * data to the subordinate output stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 * @deprecated since 2.0.1, to be removed in 3.0. Use the same class in
 * zerustech/postscript package instead.
 */
class BinaryToAsciiHexadecimalOutputStream extends PostscriptBinaryToAsciiHexadecimalOutputStream
{
}
