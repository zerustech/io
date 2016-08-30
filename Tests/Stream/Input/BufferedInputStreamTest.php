<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For full copyright and license information, please view the LICENSE file that
 * was distributed with this source code.
 */

namespace ZerusTech\Component\IO\Tests\Stream\Input;

use ZerusTech\Component\IO\Stream\Input\BufferedInputStream;
use ZerusTech\Component\IO\Stream\Input\StringInputStream;

/**
 * Test case for buffered input stream.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class BufferedInputStreamTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->ref = new \ReflectionClass('ZerusTech\Component\IO\Stream\Input\BufferedInputStream');

        $this->buffer = $this->ref->getProperty('buffer');
        $this->buffer->setAccessible(true);

        $this->bufferSize = $this->ref->getProperty('bufferSize');
        $this->bufferSize->setAccessible(true);

        $this->offset = $this->ref->getProperty('offset');
        $this->offset->setAccessible(true);

        $this->count = $this->ref->getProperty('count');
        $this->count->setAccessible(true);

        $this->mark = $this->ref->getProperty('mark');
        $this->mark->setAccessible(true);

        $this->markLimit = $this->ref->getProperty('markLimit');
        $this->markLimit->setAccessible(true);

        $this->input = $this->ref->getMethod('input');
        $this->input->setAccessible(true);

        $this->fillBuffer = $this->ref->getMethod('fillBuffer');
        $this->fillBuffer->setAccessible(true);
    }

    public function tearDown()
    {
        $this->buffer = $this->bufferSize = $this->count = null;
        $this->mark = $this->markLimit = $this->offset = null;
        $this->input = $this->fillBuffer = null;
        $this->ref = null;
    }

    public function testConstructor()
    {
        $in = new StringInputStream('hello, world');
        $instance = new BufferedInputStream($in, 4);

        $this->assertEquals(4, $this->bufferSize->getValue($instance));
        $this->assertEquals(0, $this->offset->getValue($instance));
        $this->assertEquals(0, $this->count->getValue($instance));
        $this->assertEquals(-1, $this->mark->getValue($instance));
        $this->assertEquals(0, $this->markLimit->getValue($instance));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorWithInvalidArgumentException()
    {
        $in = new StringInputStream('abc');
        $instance = new BufferedInputStream($in, 0);
    }

    /**
     * The fillBuffer() method is called only when there is no byte available in
     * the buffer (mark === count).
     * All test cases in this method are based on this pre-requisite.
     */
    public function testFillBuffer()
    {

        $buffer = $this->buffer;

        $offset = $this->offset;

        $count = $this->count;

        $mark = $this->mark;

        $markLimit = $this->markLimit;

        $fillBuffer = $this->fillBuffer;

        // No mark exists (mark === -1), the method reads up to 'buffer size'
        // bytes from the subordinate stream and overwrites current buffer with
        // those bytes, so the number of bytes in the buffer can not surpass the
        // value of 'buffer size'.
        // ---------------------------------------------------------------------

        //
        // In this case, the buffer is empty and the number of bytes in the
        // subordinate stream is greater than the 'buffer size'.
        //
        // The scenario is as follows:
        //
        // mark: -1
        // limit: 4
        // buffer size: 4
        //
        // Before filling:
        // -----------------
        // buffer:
        // mark:  ^
        // pos:    ^
        // count:  ^
        // available: 123456789ABCDEF
        //
        // After filling:
        // -----------------
        // buffer: 1234
        // mark:  ^
        // pos:    ^
        // count:      ^
        // available: 56789ABCDEF
        $in = new StringInputStream('123456789ABCDEF');
        $instance = new BufferedInputStream($in, 4);
        $markLimit->setValue($instance, 4);
        $mark->setValue($instance, -1);
        $buffer->setValue($instance, '');
        $offset->setValue($instance, 0);
        $count->setValue($instance, 0);
        $fillBuffer->invoke($instance);
        $this->assertEquals('1234', $buffer->getValue($instance));
        $this->assertEquals(-1, $mark->getValue($instance));
        $this->assertEquals(0, $offset->getValue($instance));
        $this->assertEquals(4, $count->getValue($instance));


        // In this case, there are exactly 'buffer size' bytes in buffer and the
        // number of bytes in the subordinate stream is less than the
        // 'buffer size'.
        //
        // The scenario is as follows:
        //
        // mark: -1
        // limit: 4
        // buffer size: 4
        //
        // Before filling:
        // -----------------
        // buffer: GHIJ
        // mark:  ^
        // pos:        ^
        // count:      ^
        // available: 123
        //
        // After filling:
        // -----------------
        // buffer: 123
        // mark:  ^
        // pos:    ^
        // count:     ^
        // available:
        $in = new StringInputStream('123');
        $instance = new BufferedInputStream($in, 4);
        $markLimit->setValue($instance, 4);
        $mark->setValue($instance, -1);
        $buffer->setValue($instance, 'GHIJ');
        $offset->setValue($instance, 4);
        $count->setValue($instance, 4);
        $fillBuffer->invoke($instance);
        $this->assertEquals('123', $buffer->getValue($instance));
        $this->assertEquals(-1, $mark->getValue($instance));
        $this->assertEquals(0, $offset->getValue($instance));
        $this->assertEquals(3, $count->getValue($instance));

        // In this case, both the buffer and the subordinate stream are empty.
        //
        // The scenario is as follows:
        //
        // mark: -1
        // limit: 4
        // buffer size: 4
        //
        // Before filling:
        // -----------------
        // buffer:
        // mark:  ^
        // pos:    ^
        // count:  ^
        // available:
        //
        // After filling:
        // -----------------
        // buffer:
        // mark:  ^
        // pos:    ^
        // count:  ^
        // available:
        $in = new StringInputStream('');
        $instance = new BufferedInputStream($in, 4);
        $markLimit->setValue($instance, 4);
        $mark->setValue($instance, -1);
        $buffer->setValue($instance, '');
        $offset->setValue($instance, 0);
        $count->setValue($instance, 0);
        $fillBuffer->invoke($instance);
        $this->assertEquals('', $buffer->getValue($instance));
        $this->assertEquals(-1, $mark->getValue($instance));
        $this->assertEquals(0, $offset->getValue($instance));
        $this->assertEquals(0, $count->getValue($instance));


        // Mark exists (mark >= 0) and is still valid: pos - mark < limit, the
        // method shifts all bytes after mark inclusively to the begining of the
        // buffer, then reads up to 'buffer size' bytes from the subordinate
        // stream and appends those bytes to the new end of the buffer. So the
        // number of bytes in the buffer can surpass the value of 'buffer size'
        // after filling.
        //
        // NOTE: when mark equals to 0, no byte will be shifted.
        // --------------------------------------------------------------------

        // In this case, the number of bytes in the buffer and the subordinate
        // stream are both greater than the 'buffer size'.
        //
        // The scenario is as follows:
        //
        // mark: 1
        // limit: 8
        // buffer Size: 4
        //
        // Before filling:
        // -----------------
        // buffer: GHIJK
        // mark:    ^
        // pos:         ^
        // count:       ^
        // available: 123456789ABCDEF
        //
        // After filling:
        // -----------------
        // buffer: HIJK1234
        // mark:   ^
        // pos:       ^
        // count:          ^
        // available: 56789ABCDEF
        $in = new StringInputStream('123456789ABCDEF');
        $instance = new BufferedInputStream($in, 4);
        $markLimit->setValue($instance, 8);
        $mark->setValue($instance, 1);
        $buffer->setValue($instance, 'GHIJK');
        $offset->setValue($instance, 5);
        $count->setValue($instance, 5);
        $fillBuffer->invoke($instance);
        $this->assertEquals('HIJK1234', $buffer->getValue($instance));
        $this->assertEquals(0, $mark->getValue($instance));
        $this->assertEquals(4, $offset->getValue($instance));
        $this->assertEquals(8, $count->getValue($instance));

        // In this case, the number of bytes in the buffer equals to the
        // 'buffer size' and which of the subordinate stream is less than the
        // 'buffer size'.
        //
        // The scenario is as follows:
        //
        // mark: 1
        // limit: 8
        // buffer Size: 4
        //
        // Before filling:
        // -----------------
        // buffer: GHIJ
        // mark:    ^
        // pos:        ^
        // count:      ^
        // available: 123
        //
        // After filling:
        // -----------------
        // buffer: HIJ123
        // mark:   ^
        // pos:       ^
        // count:        ^
        // available:
        $in = new StringInputStream('123');
        $instance = new BufferedInputStream($in, 4);
        $markLimit->setValue($instance, 8);
        $mark->setValue($instance, 1);
        $buffer->setValue($instance, 'GHIJ');
        $offset->setValue($instance, 4);
        $count->setValue($instance, 4);
        $fillBuffer->invoke($instance);
        $this->assertEquals('HIJ123', $buffer->getValue($instance));
        $this->assertEquals(0, $mark->getValue($instance));
        $this->assertEquals(3, $offset->getValue($instance));
        $this->assertEquals(6, $count->getValue($instance));


        // In this case, the number of bytes in the buffer is greater than the
        // 'buffer size' and the subordinate stream is empty.
        //
        // The scenario is as follows:
        //
        // mark: 1
        // limit: 8
        // buffer Size: 4
        //
        //
        // Before filling:
        // -----------------
        // buffer: GHIJK
        // mark:    ^
        // pos:         ^
        // count:       ^
        // available:
        //
        // After filling:
        // -----------------
        // buffer: HIJK
        // mark:   ^
        // pos:        ^
        // count:      ^
        // available:
        $in = new StringInputStream('');
        $instance = new BufferedInputStream($in, 4);
        $markLimit->setValue($instance, 8);
        $mark->setValue($instance, 1);
        $buffer->setValue($instance, 'GHIJK');
        $offset->setValue($instance, 5);
        $count->setValue($instance, 5);
        $fillBuffer->invoke($instance);
        $this->assertEquals('HIJK', $buffer->getValue($instance));
        $this->assertEquals(0, $mark->getValue($instance));
        $this->assertEquals(4, $offset->getValue($instance));
        $this->assertEquals(4, $count->getValue($instance));

        // In this case, the number of bytes in the buffer and the subordinate
        // stream are both greater than the 'buffer size'.
        //
        // The scenario is as follows:
        //
        // mark: 0
        // limit: 8
        // buffer Size: 4
        //
        // Before filling:
        // -----------------
        // buffer: GHIJK
        // mark:   ^
        // pos:         ^
        // count:       ^
        // available: 123456789ABCDEF
        //
        // After filling:
        // -----------------
        // buffer: GHIJK1234
        // mark:   ^
        // pos:         ^
        // count:           ^
        // available: 56789ABCDEF
        $in = new StringInputStream('123456789ABCDEF');
        $instance = new BufferedInputStream($in, 4);
        $markLimit->setValue($instance, 8);
        $mark->setValue($instance, 0);
        $buffer->setValue($instance, 'GHIJK');
        $offset->setValue($instance, 5);
        $count->setValue($instance, 5);
        $fillBuffer->invoke($instance);
        $this->assertEquals('GHIJK1234', $buffer->getValue($instance));
        $this->assertEquals(0, $mark->getValue($instance));
        $this->assertEquals(5, $offset->getValue($instance));
        $this->assertEquals(9, $count->getValue($instance));


        // In this case, the number of bytes in the buffer is equal to the
        // buffer size and which of the subordinate stream is greater than the
        // buffer size.
        //
        // The scenario is as follows:
        //
        // mark: 5
        // limit: 3
        // buffer Size: 6
        //
        // Before filling:
        // -----------------
        // buffer: GHIJKL
        // mark:        ^
        // pos:          ^
        // count:        ^
        // available: 123456789ABCDEF
        //
        // After filling:
        // -----------------
        // buffer: L123456
        // mark:   ^
        // pos:     ^
        // count:         ^
        // available: 789ABCDEF
        //
        // NOTE: in this case, after the buffer is filled, there are more than
        // mark limit bytes after the mark in current buffer and the mark is
        // still valid. So the offset can be grown to surpass the mark limit
        // without reading more bytes from the underlying stream, therefore the
        // mark can remain valid even the mark limit has actually been exceeded.
        $in = new StringInputStream('123456789ABCDEF');
        $instance = new BufferedInputStream($in, 6);
        $markLimit->setValue($instance, 3);
        $mark->setValue($instance, 5);
        $buffer->setValue($instance, 'GHIJKL');
        $offset->setValue($instance, 6);
        $count->setValue($instance, 6);
        $fillBuffer->invoke($instance);
        $this->assertEquals('L123456', $buffer->getValue($instance));
        $this->assertEquals(0, $mark->getValue($instance));
        $this->assertEquals(1, $offset->getValue($instance));
        $this->assertEquals(7, $count->getValue($instance));


        // Mark exists (mark > 0), but has become invalid: pos - mark >= limit,
        // the method reads up to 'buffer size' bytes from the subordinate
        // stream and overwrites current buffer with those bytes. So the
        // number of bytes in the buffer can not surpass the value of
        // 'buffer size' after filling.
        // -------------------------------------------------------------------

        // In this case, the number of bytes in the buffer and the subordinate
        // stream are both greater than the 'buffer size' and the number of
        // bytes read after mark is greater than the limit (pos - mark > limit).
        //
        // The scenario is as follows:
        //
        // mark: 1
        // limit: 3
        // buffer Size: 6
        //
        // Before filling:
        // -----------------
        // buffer: GHIJK
        // mark:    ^
        // pos:         ^
        // count:       ^
        // available: 123456789ABCDEF
        //
        // After filling:
        // -----------------
        // buffer: 123456
        // mark:  ^
        // pos:    ^
        // count:        ^
        // available: 789ABCDEF
        $in = new StringInputStream('123456789ABCDEF');
        $instance = new BufferedInputStream($in, 6);
        $markLimit->setValue($instance, 3);
        $mark->setValue($instance, 1);
        $buffer->setValue($instance, 'GHIJK');
        $offset->setValue($instance, 5);
        $count->setValue($instance, 5);
        $fillBuffer->invoke($instance);
        $this->assertEquals('123456', $buffer->getValue($instance));
        $this->assertEquals(-1, $mark->getValue($instance));
        $this->assertEquals(0, $offset->getValue($instance));
        $this->assertEquals(6, $count->getValue($instance));


        // In this case, the number of bytes in the buffer is greater than
        // 'buffer size' and which of the subordinate stream is less than the
        // 'buffer size'. The number of bytes read after mark equals to the
        // limit (pos - mark === limit).
        //
        //
        // The scenario is as follows:
        //
        // mark: 1
        // limit: 3
        // buffer Size: 6
        //
        // Before filling:
        // -----------------
        // buffer: GHIJK
        // mark:    ^
        // pos:         ^
        // count:       ^
        // available: 123
        //
        // After filling:
        // -----------------
        // buffer: 123
        // mark:  ^
        // pos:    ^
        // count:     ^
        // available:
        $in = new StringInputStream('123');
        $instance = new BufferedInputStream($in, 6);
        $markLimit->setValue($instance, 3);
        $mark->setValue($instance, 1);
        $buffer->setValue($instance, 'GHIJK');
        $offset->setValue($instance, 5);
        $count->setValue($instance, 5);
        $fillBuffer->invoke($instance);
        $this->assertEquals('123', $buffer->getValue($instance));
        $this->assertEquals(-1, $mark->getValue($instance));
        $this->assertEquals(0, $offset->getValue($instance));
        $this->assertEquals(3, $count->getValue($instance));
    }

    public function testInput()
    {
        $in = new StringInputStream('123456789ABCDEF');
        $instance = new BufferedInputStream($in, 4);

        $this->assertEquals(1, $this->input->invokeArgs($instance, [&$bytes, 1]));
        $this->assertEquals('1', $bytes);
        $this->assertEquals('1234', $this->buffer->getValue($instance));
        $this->assertEquals(1, $this->offset->getValue($instance));
        $this->assertEquals(14, $instance->available());

        $this->assertEquals(5, $this->input->invokeArgs($instance, [&$bytes, 5]));
        $this->assertEquals('23456', $bytes);
        $this->assertEquals('5678', $this->buffer->getValue($instance));
        $this->assertEquals(2, $this->offset->getValue($instance));
        $this->assertEquals(9, $instance->available());

        $this->assertEquals(9, $this->input->invokeArgs($instance, [&$bytes, 9]));
        $this->assertEquals('789ABCDEF', $bytes);
        $this->assertEquals('', $this->buffer->getValue($instance));
        $this->assertEquals(0, $this->offset->getValue($instance));
        $this->assertEquals(0, $instance->available());

        $this->assertEquals(-1, $this->input->invokeArgs($instance, [&$bytes, 1]));
        $this->assertEquals('', $bytes);
        $this->assertEquals('', $this->buffer->getValue($instance));
        $this->assertEquals(0, $instance->available());
    }

    public function testMarkAndReset()
    {
        $in = new StringInputStream('123456789ABCDEF');
        $instance = new BufferedInputStream($in, 4);

        // 1234
        //^ (mark)
        //   ^ (pos)
        //     ^ (count)
        $this->assertEquals(2, $instance->read($bytes, 2));
        $this->assertEquals('12', $bytes);
        $this->assertEquals(13, $instance->available());

        // 1234
        //   ^ (mark)
        //   ^ (pos)
        //     ^ (count)
        $instance->mark(4);

        // 345678
        // ^ (mark)
        //    ^ (pos)
        //       ^ (count)
        $this->assertEquals(3, $instance->read($bytes, 3));
        $this->assertEquals('345', $bytes);
        $this->assertEquals(10, $instance->available());

        // 345678
        // ^ (mark)
        // ^ (pos)
        //       ^ (count)
        $instance->reset();
        $this->assertEquals(13, $instance->available());

        // 345678
        // ^ (mark)
        //    ^ (pos)
        //       ^ (count)
        $this->assertEquals(3, $instance->read($bytes, 3));
        $this->assertEquals('345', $bytes);
        $this->assertEquals(10, $instance->available());

        //  9ABC
        // ^ (mark)
        //  ^ (pos)
        //      ^ (count)
        $this->assertEquals(3, $instance->read($bytes, 3));
        $this->assertEquals('678', $bytes);
        $this->assertEquals(7, $instance->available());
    }

    public function testValidInvalidMark()
    {
        $in = new StringInputStream('GHIJKL123456789ABCDEF');
        $instance = new BufferedInputStream($in, 6);

        //  GHIJKL
        // ^ (mark)
        //       ^ (pos)
        //        ^ (count)
        $this->assertEquals(5, $instance->read($bytes, 5));
        $this->assertEquals('GHIJK', $bytes);
        $this->assertEquals(16, $instance->available());

        //  GHIJKL
        //       ^ (mark)
        //       ^ (pos)
        //        ^ (count)
        $instance->mark(3);

        //  L12345
        //  ^ (mark)
        //      ^ (pos)
        //        ^ (count)
        //
        // NOTE: Now more than mark limit bytes have been read after mark, so
        // technically the mark should be invalidated. But it won't because
        // there are still some bytes available in current buffer, so there is
        // no need to refill current buffer, therefore, the mark remains valid
        // under this circumstance.
        $this->assertEquals(5, $instance->read($bytes, 5));
        $this->assertEquals('L1234', $bytes);
        $this->assertEquals(11, $instance->available());

        //  L12345
        //  ^ (mark)
        //  ^ (pos)
        //        ^ (count)
        //
        $instance->reset();
        $this->assertEquals(16, $instance->available());

        //  L12345
        //  ^ (mark)
        //       ^ (pos)
        //        ^ (count)
        //
        $this->assertEquals(5, $instance->read($bytes, 5));
        $this->assertEquals('L1234', $bytes);
        $this->assertEquals(11, $instance->available());
    }

    /**
     * @dataProvider getDataForTestInvalidMark
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessage Invalid mark.
     */
    public function testInvalidMark($instance)
    {
        $instance->reset();
    }

    public function getDataForTestInvalidMark()
    {
        $data = [];

        $in = new StringInputStream('123456789ABCDEF');
        $instance = new BufferedInputStream($in, 4);
        $data[][] = $instance;

        $in = new StringInputStream('123456789ABCDEF');
        $instance = new BufferedInputStream($in, 4);
        $instance->read($bytes, 2);
        $instance->mark(4);
        $instance->read($bytes, 5);

        $in = new StringInputStream('123456789ABCDEF');
        $instance = new BufferedInputStream($in, 4);
        $instance->read($bytes, 2);
        $instance->mark(4);
        $instance->read($bytes, 6);
        $data[][] = $instance;

        return $data;
    }

    /**
     * @expectedException ZerusTech\Component\IO\Exception\IOException
     * @expectedExceptionMessage Stream closed.
     */
    public function testResetOnClosedStream()
    {
        $in = new StringInputStream('123456789ABCDEF');
        $instance = new BufferedInputStream($in, 4);
        $instance->read($bytes, 2);
        $instance->mark(4);
        $instance->close();
        $instance->reset();
    }

    public function testMiscMethods()
    {
        $in = new StringInputStream('0123456789ABCDEF');
        $instance = new BufferedInputStream($in, 4);

        $this->assertTrue($instance->markSupported());

        $instance->skip(2);
        $instance->mark(4);

        $this->assertFalse($instance->isClosed());
        $this->assertEquals(2, $this->mark->getValue($instance));
        $this->assertEquals(2, $this->offset->getValue($instance));

        $instance->close();
        $this->assertTrue($instance->isClosed());
        $this->assertEquals(-1, $this->mark->getValue($instance));
        $this->assertEquals(0, $this->offset->getValue($instance));
    }
}
