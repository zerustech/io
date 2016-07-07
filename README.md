ZerusTech IO Component
================================================
The *ZerusTech IO Component* provides some commonly used I/O related classes,
such as some input stream and output stream classes.

This library was developed along with the [zerustech/terminal][1] component, when we
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

### FileOutputStream ###

A file output stream is an output stream for writing data to a file.

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use ZerusTech\Component\IO\Stream\Output\FileOutputStream;

$out = new FileOutputStream('foo.txt', 'wb');

$out->write('hello, world!');

```

References
----------
* [The zerustech/terminal project][1]

[1]:  https://github.com/zerustech/terminal "The zerustech/terminal Project"
[2]:  https://opensource.org/licenses/MIT "The MIT License (MIT)"

License
-------
The *ZerusTech IO Component* is published under the [MIT License][2].
