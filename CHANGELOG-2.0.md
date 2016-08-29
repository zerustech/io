CHANGELOG for 2.0.x
=====================

This changelog references the relavant changes (bug and security fixes) done in
2.0 minor versions.

To get the new features in this major release, check the list at the bottom of
this file.

* 2.0.2 (2016-08-29)
    * It was incorrect about ``input()`` method in change log 2.0.1, in fact,
      length can't be zeror. 
    * Optimized code in ``input()`` methods, to reflect the fact that length is
      always greater than 0.
    * Method ``receive()`` should return the actual number of bytes received.
    * Updated test scripts to assert the return value of ``input()``,
      ``receive()`` and ``output()`` methods.

* 2.0.1 (2016-08-26)
    * In readSubstring(), allows the computed offset to be zero, if source
      string is ''.
    * In readSubstring(), allows the computed length to be zero, so that empty
      string is allowed to be read.
    * In input(), when length is zero, it returns 0, instead of -1. 
    * In writeSubstring(), allows the computed offset to be zero, if source
      string is ''.
    * In writeSubstring(), allows the computed length to be zero, so that empty
      string is allowed to be written.
    * Deprecated AsciiHexadecimalToBinaryInputStream and moved it to
      zerustech/postscript package.
    * Deprecated BinaryToAsciiHexadecimalOutputStream and moved it to
      zerustech/postscript package.
    * Fixed inconsistent parameter names in method input()
    * Code optimization for handling zero length in readSubstring() method.

* 2.0.0 (2016-08-20)
    * Removed getResource() method
    * Removed offset() method
    * Changed the signature of function write() for all output streams. 
    * Changed the signature of function read() for all input streams.
