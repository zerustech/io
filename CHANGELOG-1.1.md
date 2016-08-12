CHANGELOG for 1.1.x
=====================

This changelog references the relavant changes (bug and security fixes) done in
1.1 minor versions.

To get the new features in this major release, check the list at the bottom of
this file.

* 1.1.5
    * Changed accessibility of FilterInputStream::in to protected 
    * Changed signature of OutputStreamInterface::write()
    * Updated comments for FilterInputStream
    * Deprecated FilterInputStream::getResource()

* 1.1.4 (2016-08-11)
    * Restore dev-master alias

* 1.1.3 (2016-08-10)
    * Moved source files to ``./src``.

* 1.1.2 (2016-08-10)
    * Add sample for ``BufferedInputStream``
    * Removed ``getBuffer()`` from ``StringInputStream``
    * Removed ``getPosition()`` from ``StringInputStream`` 

* 1.1.1 (2016-08-10)
    * Changed alias of dev-master to 1.2-dev

* 1.1.0 (2016-08-10)
    * Removed class ``AbstractInputStream``.
    * Added method ``available()`` for all input stream classes.
    * Added method ``mark()`` for all input stream classes.
    * Added method ``markSupported()`` for all input stream classes.
    * Added method ``reset()`` for all input stream classes.
    * Added method ``skip()`` for all input stream classes.
    * Added class ``AbstractInputStream``.
    * Added class ``FilterInputStream``.
    * Added class ``BufferedInputStream``.
    * Added class ``AbstractOutputStream``.
    * Removed ``composer.lock``
