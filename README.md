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

$count = $input->read($string, 10); // reads upto 10 bytes from foo.txt

printf("%d bytes read: %s\n", $count, $string);

```

### StringInputStream ###

This class allows an application to create an input stream in which the bytes
are supplied by the contents of a string.

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use ZerusTech\Component\IO\Stream\Input\StringInputStream;

$input = new StringInputStream("hello, world!");

$count = $input->read($string, 5); // returns 'hello'

printf("%d bytes read: %s\n", $count, $string);

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
$count = $buffer->read($string, 5);

printf("%d bytes read: %s\n", $count, $string); // "23456"

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
$count = $buffer->read($string, 3);

printf("%d bytes read: %s\n", $count, $string); // "234"

//  buffer:
//  -------
//  89AB
// ^ (mark)
//   ^ (pos)
//      ^ (count)
$count = $buffer->read($string, 4);

printf("%d bytes read: %s\n", $count, $string); // "5678"

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

$count = $input->read($string, 1024); // returns 'hello'

printf("%d bytes read: %s\n", $count, $string);

```

### FileOutputStream ###

A file output stream is an output stream for writing data to a file.

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use ZerusTech\Component\IO\Stream\Output\FileOutputStream;

$out = new FileOutputStream('foo.txt', 'wb');

$count = $out->write('hello, world!');

printf("%d bytes written.\n", $count);

```

### StringOutputStream ###

This class writes bytes to a string. This is useful for connecting a string with other
streams.

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use ZerusTech\Component\IO\Stream\Output\StringOutputStream;

$out = new StringOutputStream();

$count = $out->write('hello');

printf("%d bytes written: %s\n", $count, $out->__toString()); // "hello"

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

$count = $out->write("hello");

printf("%d bytes written: %s\n", $count, $target->__toString()); // "68656C6C6F"

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

$count = $input->read($data, 5); // returns 'hello'

printf("%d bytes read: %s\n", $count, $data);

```

### FilterInputStreamFactoryInterface and FilterInputStreamFactory

Factory classes that implement the ``FilterInputStreamFactoryInterface``
interface, creates specific filter input stream instances.

The factory classes should also implement the ``support()`` method to test if
the filter input stream supports the data format of the subordinate input
stream.

It's typically used by a filter input stream factory resolver to resolve a
suitable factory.

The ``FilterInputStreamFactory`` is the default implementation of the factory
interface, which supports any buffered input stream.

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use ZerusTech\Component\IO\Stream\Input\Factory\FilterInputStreamFactory;
use ZerusTech\Component\IO\Stream\Input\StringInputStream;
use ZerusTech\Component\IO\Stream\Input\BufferedInputStream;

// In order to detect the data format, the factory need fetch some leading bytes
// from the subordinate stream, so it must support mark and reset.
$in = new BufferedInputStream(new StringInputStream('hello'));

$factory = new FilterInputStreamFactory();

if ($factory->support($in)) {

    $filter = $factory->create($in);

    $count = $filter->read($string, 5);

    printf("%d bytes read: %s\n", $count, $string);
}

```

### FilterInputStreamResolverInterface and FilterInputStreamResolver ###

This interface detects the data format of the input stream and resolves it to a
filter input stream that is suitable for parsing the data format.

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use ZerusTech\Component\IO\Stream\Input\Factory\FilterInputStreamFactory;
use ZerusTech\Component\IO\Stream\Input\Resolver\FilterInputStreamResolver;
use ZerusTech\Component\IO\Stream\Input\StringInputStream;
use ZerusTech\Component\IO\Stream\Input\BufferedInputStream;

$in = new BufferedInputStream(new StringInputStream('hello'));
$factory = new FilterInputStreamFactory();
$resolver = new FilterInputStreamResolver();
$resolver->addFactory($factory);

if (null !==($filter = $resolver->resolve($in))) {

    $count = $filter->read($string, 5);

    printf("%d bytes read: %s\n", $count, $string);
}

```

References
----------
* [The zerustech/io project][2]
* [The zerustech/threaded project][3]
* [The zerustech/terminal project][4]
* [The zerustech/postscript project][5]

[1]:  https://opensource.org/licenses/MIT "The MIT License (MIT)"
[2]:  https://github.com/zerustech/io "The zerustech/io Project"
[3]:  https://github.com/zerustech/threaded "The zerustech/threaded Project"
[4]:  https://github.com/zerustech/terminal "The zerustech/terminal Project"
[5]:  https://github.com/zerustech/postscript "The zerustech/postscript Project"

License
-------
The *ZerusTech IO Component* is published under the [MIT License][1].
