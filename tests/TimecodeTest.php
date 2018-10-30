<?php

use PHPUnit\Framework\TestCase;
use FireWorkWeb\SMPTE\Timecode;

class TimecodeTest extends TestCase
{
    /**
     * @dataProvider frameRateSupportedProvider
     */
    public function testFrameRateSupported($frameRate)
    {
        $this->assertTrue(Timecode::isFrameRateSupported($frameRate));
    }

    /**
     * @dataProvider frameRateNotSupportedProvider
     */
    public function testFrameRateNotSupported($frameRate)
    {
        $this->assertFalse(Timecode::isFrameRateSupported($frameRate));
    }

    /**
     * @dataProvider validInstancesProvider
     */
    public function testValidInstances($time, $frameRate, $dropFrame)
    {
        $this->assertInstanceOf(Timecode::class, new Timecode($time, $frameRate, $dropFrame));
    }

    /**
     * @dataProvider invalidInstancesProvider
     */
    public function testInvalidInstances($time, $frameRate, $dropFrame)
    {
        $this->expectException(InvalidArgumentException::class);
        new Timecode($time, $frameRate, $dropFrame);
    }

    /**
     * @dataProvider fromSecondsProvider
     */
    public function testFromSeconds($expected, $time, $frameRate)
    {
        $this->assertEquals($expected, Timecode::fromSeconds($time, $frameRate)->getFrameCount());
    }

    /**
     * @dataProvider durationInSecondsProvider
     */
    public function testeDurationInSeconds($expected, $time, $frameRate, $dropFrame)
    {
        $this->assertEquals($expected, (new Timecode($time, $frameRate, $dropFrame))->durationInSeconds());
    }

    public function validInstancesProvider()
    {
        return [
            'test with number' => [0, 25, false],
            'test with date' => ['00:00:00:00', 25, false],
            'test with date time' => [new \DateTime('01:34:12'), 24, false],
        ];
    }

    public function invalidInstancesProvider()
    {
        return [
            'test with negative number' => [-128, 25, false],
            'test with string' => ['abc', 25, false],
            'test with boolean' => [true, 25, false],
        ];
    }

    public function fromSecondsProvider()
    {
        return [
            // should properly return frame count (23.976 fps)
            [0, 0.041, 23.976],
            [1, 0.042, 23.976],
            [1, 0.083, 23.976],
            [2, 0.084, 23.976],
            [7200, 300.301, 23.976],
            [14400, 600.601, 23.976],
            // should properly return frame count (24 fps)
            [0, 0, 24],
            [0, 0.021, 24],
            [0, 0.039, 24],
            [1, 0.042, 24],
            [2, 0.084, 24],
            [7200, 300, 24],
            [14400, 600, 24],
            // should properly return frame count (25 fps)
            [0, 0.039, 25],
            [1, 0.040, 25],
            [1, 0.079, 25],
            [2, 0.080, 25],
            [7500, 300, 25],
            [15000, 600, 25],
            // should properly return frame count (29.97 fps)
            [0, 0.033, 29.97],
            [1, 0.034, 29.97],
            [1, 0.066, 29.97],
            [2, 0.067, 29.97],
            [8991, 300, 29.97],
            [17982, 600, 29.97],
            // should properly return frame count (30 fps)
            [0, 0.033, 30],
            [1, 0.034, 30],
            [1, 0.066, 30],
            [2, 0.067, 30],
            [9000, 300, 30],
            [18000, 600, 30],
        ];
    }

    public function frameRateSupportedProvider()
    {
        return [
            [23.976],
            [24],
            [25],
            [29.97],
            [30],
        ];
    }

    public function frameRateNotSupportedProvider()
    {
        return [
            [23],
            [26],
            [1],
            [35],
            [100],
        ];
    }

    public function durationInSecondsProvider()
    {
        return [
            [518, 12443, 23.976, false],
            [2597, '00:43:14:12', 23.976, false],
            [350, 8400, 24, false],
            [6190, '01:43:10:00', 24, false],
            [23, 576, 25, false],
            [1015, '00:16:55:24', 25, false],
            [83, 2500, 29.97, true],
            [3253, '00:54:13;25', 29.97, true],
            [210, 6323, 30, false],
            [10813, '03:00:13:27', 30, false],
        ];
    }
}
