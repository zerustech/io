CHANGELOG for 1.2.x
=====================

This changelog references the relavant changes (bug and security fixes) done in
1.2 minor versions.

To get the new features in this major release, check the list at the bottom of
this file.

* 1.2.5 ()
    * Changed AsciiHexadecimalToBinaryInputStream::isSpace to a public static
      method.
    * Added hasNonHex() method to AsciiHexadecimalToBinaryInputStream

* 1.2.4 (2016-08-13)
    * Merged 1.1
    * Fixed CS issue

* 1.2.3 (2016-08-13)
    * Added code samples.

* 1.2.2 (2016-08-13)
    * Removed dependency on zerustech/threaded to avoid circular dependency

* 1.2.1 (2016-08-13)
    * Install pthreads for travis 

* 1.2.0 (2016-08-13)
    * Added class ``AsciiHexadecimalToBinaryInputStream``.
    * Added class ``FilterOutputStream``.
    * Added class ``StringOutputStream``.
    * Added class ``BinaryToAsciiHexadecimalOutputStream``.
    * Added require-dev for zerustech/threade.
