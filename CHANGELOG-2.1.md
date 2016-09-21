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
    * Removed position property from all stream classes
    * Added bufferSize argument to skip() method
    * Removed default value of length argument from input() method.
    * Re-implemented the logic for available() method
    * Re-implemented the logic of number of bytes returned by input() method.
    * Added readLine() method to line input stream.
    * Added wash output stream.
    * Added wash input stream.
    * Added uncountable filter input stream.
    * Added bufferable filter input stream.
    * Added uncountable bufferable filter input stream.

