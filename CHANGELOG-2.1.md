CHANGELOG for 2.1.x
=====================

This changelog references the relavant changes (bug and security fixes) done in
2.1 minor versions.

To get the new features in this major release, check the list at the bottom of
this file.

* 2.1.0 ()
    * Added LineInputStream and updated README.md accordingly.
    * Renamed BufferedInputStream::position to offset
    * Complete logic for position for all input stream.
    * Complete logic for available for all input stream.
    * Complete logic for 'number of bytes read' for all input stream.
    * Removed AsciiHexadecimalToBinaryInputStream
    * Removed BinaryToAsciiHexadecimalOutputStream
    * Updated test case to test number of bytes read, available and position.
    * Double checked all output test scripts and make sure the number of bytes
      written has been tested.
