[![Build Status](https://api.travis-ci.org/zerustech/io.svg)](https://travis-ci.org/zerustech/io)

ZerusTech IO Component
================================================
The *ZerusTech IO Component* provides some commonly used I/O related classes,
such as some input stream and output stream classes.

This library was developed along with the [zerustech/terminal][4] component, when we
could not find the stream implementations that best fit our requirements.

Installation
-------------

You can install this component in 2 different ways:

* Install it via Composer
```bash
$ cd <project-root-directory>
$ composer require zerustech/io
```

* Use the official Git repository (https://github.com/zerustech/io)

Examples
-------------

### FileInputStream ###

A file input stream obtains input bytes from a file.

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use ZerusTech\Component\IO\Stream\Input\FileInputStream;

$input = new FileInputStream('foo.txt', 'rb');

$string = $input->read(10); // reads upto 10 bytes from foo.txt

printf("%s\n", $string);

```

### StringInputStream ###

This class allows an application to create an input stream in which the bytes
are supplied by the contents of a string.

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use ZerusTech\Component\IO\Stream\Input\StringInputStream;

$input = new StringInputStream("hello, world!");

$string = $input->read(5); // returns 'hello'

printf("%s\n", $string);

```

### BufferedInputStream ###

This class allows an application to create an input stream that buffers input
from an underlying implementation.

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use ZerusTech\Component\IO\Stream\Input\StringInputStream;
use ZerusTech\Component\IO\Stream\Input\BufferedInputStream;

$input = new StringInputStream("0123456789ABCDEF");

$buffer = new BufferedInputStream($input, 4);

//  buffer:
//  ------
//  0123 
// ^ (mark)
//    ^ (pos)
//      ^ (count)
$buffer->skip(2);

//  buffer:
//  -------
//  0123 
//    ^ (mark)
//    ^ (pos)
//      ^ (count)
$buffer->mark(6);

//  buffer:
//  -------
//  01234567
//    ^ (mark)
//         ^ (pos)
//          ^ (count)
$string = $buffer->read(5);

printf("%s\n", $string); // "23456"

//  buffer:
//  -------
//  01234567
//    ^ (mark)
//    ^ (pos)
//          ^ (count)
$buffer->reset();

//  buffer:
//  -------
//  01234567
//    ^ (mark)
//       ^ (pos)
//          ^ (count)
$string = $buffer->read(3);

printf("%s\n", $string); // "234"

//  buffer:
//  -------
//  89AB
// ^ (mark)
//   ^ (pos)
//      ^ (count)
$string = $buffer->read(4);

printf("%s\n", $string); // "5678"

```

### AsciiHexadecimalToBinaryInputStream ###

This class reads bytes from a subordinate stream and converts the bytes read
from ``Ascii Hexadecimal`` format into ``Binary`` format.

In ``Ascii Hexadecimal`` format, spaces (``" ", "\t", "\n", "\r"``) are ignored.

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use ZerusTech\Component\IO\Stream\Input\StringInputStream;
use ZerusTech\Component\IO\Stream\Input\AsciiHexadecimalToBinaryInputStream;

$src = new StringInputStream("68656C6C 6F");

$input = new AsciiHexadecimalToBinaryInputStream($src);

$string = $input->read(1024); // returns 'hello'

printf("%s\n", $string);

```

### FileOutputStream ###

A file output stream is an output stream for writing data to a file.

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use ZerusTech\Component\IO\Stream\Output\FileOutputStream;

$out = new FileOutputStream('foo.txt', 'wb');

$out->write('hello, world!');

```

### StringOutputStream ###

This class writes bytes to a string. This is useful for connecting a string with other
streams.

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use ZerusTech\Component\IO\Stream\Output\StringOutputStream;

$out = new StringOutputStream();

$out->write('hello');

printf("%s\n", $out->__toString()); // "hello"

```

### BinaryToAsciiHexadecimalOutputStream ###

This class converts bytes that are written to it from ``Binary`` format to
``Ascii Hexadecimal format`` and writes the converted bytes to a subordinate
stream.

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use ZerusTech\Component\IO\Stream\Output\StringOutputStream;
use ZerusTech\Component\IO\Stream\Output\BinaryToAsciiHexadecimalOutputStream;

$target = new StringOutputStream();

$out = new BinaryToAsciiHexadecimalOutputStream($target);

$out->write("hello");

printf("%s\n", $target->__toString()); // "68656C6C6F"

```

### PipedInputStream and PipedOutputStream ###

The ``PipedInputStream`` and ``PipedOutputStream`` can be connected to create a 
communication pipe. The ``PipedOutputStream`` writes data to the
``PipedInputStream``. 

The implementation in this component is not thread-safe, so it does not support
wait and notify features. For piped stream classes in multi-threaded
environment, check [zerustech/threaded][3] for details.

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use ZerusTech\Component\IO\Stream\Input\PipedInputStream;
use ZerusTech\Component\IO\Stream\Output\PipedOutputStream;

$output = new PipedOutputStream();

$input = new PipedInputStream($output);

$output->write('hello');

$data = $input->read(5); // returns 'hello'

printf("%s\n", $data);

```


References
----------
* [The zerustech/io project][2]
* [The zerustech/threaded project][3]
* [The zerustech/terminal project][4]

[1]:  https://opensource.org/licenses/MIT "The MIT License (MIT)"
[2]:  https://github.com/zerustech/io "The zerustech/io Project"
[3]:  https://github.com/zerustech/threaded "The zerustech/threaded Project"
[4]:  https://github.com/zerustech/terminal "The zerustech/terminal Project"

License
-------
The *ZerusTech IO Component* is published under the [MIT License][1].
