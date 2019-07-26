<?php

namespace FireworkWeb\Tests;

use FireworkWeb\SMPTE\FrameRate;

trait TimecodeProvider
{
    public function defaultFrameRateProvider()
    {
        return [
            [FrameRate::FR_23_976],
            [FrameRate::FR_24],
            [FrameRate::FR_25],
            [FrameRate::FR_29_97],
            [FrameRate::FR_30],
            [FrameRate::FR_50],
            [FrameRate::FR_59_94],
            [FrameRate::FR_60],
        ];
    }

    public function validInstancesProvider()
    {
        return [
            'test with number' => [0, FrameRate::FR_25, false],
            'test with date' => ['00:00:00:00', FrameRate::FR_25, false],
            'test with date time' => [new \DateTime('01:34:12'), FrameRate::FR_24, false],
        ];
    }

    public function invalidInstancesProvider()
    {
        return [
            'test with negative number' => [-128, FrameRate::FR_25, false],
            'test with string' => ['abc', FrameRate::FR_25, false],
            'test with boolean' => [true, FrameRate::FR_25, false],
        ];
    }
    public function toStringProvider()
    {
        return [
            // should properly return string
            ['00:00:00:00', 0, FrameRate::FR_23_976, false],
            ['00:00:00:00', 0, FrameRate::FR_24, false],
            ['00:00:00:00', 0, FrameRate::FR_25, false],
            ['00:00:00:00', 0, FrameRate::FR_29_97, false],
            ['00:00:00;00', 0, FrameRate::FR_29_97, true],
            ['00:00:00:00', 0, FrameRate::FR_30, false],
            // should properly return string
            ['00:00:20:20', 500, FrameRate::FR_23_976, false],
            ['00:00:20:20', 500, FrameRate::FR_24, false],
            ['00:00:20:00', 500, FrameRate::FR_25, false],
            ['00:00:16:20', 500, FrameRate::FR_29_97, false],
            ['00:00:16;20', 500, FrameRate::FR_29_97, true],
            ['00:00:16:20', 500, FrameRate::FR_29_97, false],
            // should properly return string
            ['00:06:56:16', 10000, FrameRate::FR_23_976, false],
            ['00:06:56:16', 10000, FrameRate::FR_24, false],
            ['00:06:40:00', 10000, FrameRate::FR_25, false],
            ['00:05:33:10', 10000, FrameRate::FR_29_97, false],
            ['00:05:33;20', 10000, FrameRate::FR_29_97, true],
            ['00:05:33:10', 10000, FrameRate::FR_30, false],
        ];
    }

    public function fromSecondsProvider()
    {
        return [
            // should properly return frame count (FrameRate::FR_23_976 fps)
            [0, 0.041, FrameRate::FR_23_976],
            [1, 0.042, FrameRate::FR_23_976],
            [1, 0.083, FrameRate::FR_23_976],
            [2, 0.084, FrameRate::FR_23_976],
            [7201, 300.376, FrameRate::FR_23_976],
            [14403, 600.751, FrameRate::FR_23_976],
            // should properly return frame count (24 fps)
            [0, 0, FrameRate::FR_24],
            [0, 0.021, FrameRate::FR_24],
            [0, 0.039, FrameRate::FR_24],
            [1, 0.042, FrameRate::FR_24],
            [2, 0.084, FrameRate::FR_24],
            [7200, 300, FrameRate::FR_24],
            [14400, 600, FrameRate::FR_24],
            // should properly return frame count (25 fps)
            [0, 0.039, FrameRate::FR_25],
            [1, 0.040, FrameRate::FR_25],
            [1, 0.079, FrameRate::FR_25],
            [2, 0.080, FrameRate::FR_25],
            [7500, 300, FrameRate::FR_25],
            [15000, 600, FrameRate::FR_25],
            // should properly return frame count (FrameRate::FR_29_97 fps)
            [0, 0.033, FrameRate::FR_29_97],
            [1, 0.034, FrameRate::FR_29_97],
            [1, 0.066, FrameRate::FR_29_97],
            [2, 0.067, FrameRate::FR_29_97],
            [8991, 300, FrameRate::FR_29_97],
            [17982, 600, FrameRate::FR_29_97],
            // should properly return frame count (30 fps)
            [0, 0.033, FrameRate::FR_30],
            [1, 0.034, FrameRate::FR_30],
            [1, 0.066, FrameRate::FR_30],
            [2, 0.067, FrameRate::FR_30],
            [9000, 300, FrameRate::FR_30],
            [18000, 600, FrameRate::FR_30],
            //should properly return frame count (50 fps)
            [1, 0.033, FrameRate::FR_50],
            [1, 0.034, FrameRate::FR_50],
            [3, 0.066, FrameRate::FR_50],
            [4, 0.082, FrameRate::FR_50],
            [15000, 300, FrameRate::FR_50],
            [30000, 600, FrameRate::FR_50],
            //should properly return frame count (59.94 fps)
            [1, 0.033, FrameRate::FR_59_94],
            [2, 0.034, FrameRate::FR_59_94],
            [3, 0.066, FrameRate::FR_59_94],
            [4, 0.082, FrameRate::FR_59_94],
            [17982, 300, FrameRate::FR_59_94],
            [35964, 600, FrameRate::FR_59_94],
            //should properly return frame count (59.94 fps)
            [1, 0.033, FrameRate::FR_60],
            [2, 0.034, FrameRate::FR_60],
            [3, 0.066, FrameRate::FR_60],
            [4, 0.082, FrameRate::FR_60],
            [18000, 300, FrameRate::FR_60],
            [36000, 600, FrameRate::FR_60],
        ];
    }

    public function invalidFromSecondsProvider()
    {
        return [
            // should break with invalid seconds
            ['', FrameRate::FR_23_976, false],
            [false, FrameRate::FR_24, false],
            [new \DateTime(), FrameRate::FR_25, false],
            [null, FrameRate::FR_29_97, false],
            ['', FrameRate::FR_29_97, true],
            ['', FrameRate::FR_30, false],
            // should break with invalid framerate
            [1, 22, false],
            [1, 26, false],
            [1, 0, false],
            [1, 100, false],
            [1, 10000, false],
            // should break with invalid dropFrame
            [1, FrameRate::FR_23_976, true],
            [1, FrameRate::FR_24, true],
            [1, FrameRate::FR_25, true],
            [1, FrameRate::FR_30, true],
        ];
    }

    public function frameRateSupportedProvider()
    {
        return [
            [FrameRate::FR_23_976, false],
            [FrameRate::FR_24, false],
            [FrameRate::FR_25, false],
            [FrameRate::FR_29_97, false],
            [FrameRate::FR_30, false],
            [FrameRate::FR_50, false],
            [FrameRate::FR_59_94, false],
            [FrameRate::FR_60, false],
        ];
    }

    public function frameRateNotSupportedProvider()
    {
        return [
            [23, false],
            [26, false],
            [1, false],
            [35, false],
            [100, false],
        ];
    }

    public function durationInSecondsProvider()
    {
        return [
            [518, 12443, FrameRate::FR_23_976, false],
            [2597, '00:43:14:12', FrameRate::FR_23_976, false],
            [350, 8400, FrameRate::FR_24, false],
            [6190, '01:43:10:00', FrameRate::FR_24, false],
            [23, 576, FrameRate::FR_25, false],
            [1015, '00:16:55:24', FrameRate::FR_25, false],
            [83, 2500, FrameRate::FR_29_97, true],
            [3253, '00:54:13;25', FrameRate::FR_29_97, true],
            [210, 6323, FrameRate::FR_30, false],
            [10813, '03:00:13:27', FrameRate::FR_30, false],
        ];
    }

    public function addProvider()
    {
        return [
            ['00:00:00:00', '00:00:00:00', '00:00:00:00'],
            ['00:00:00:10', '00:00:00:01', '00:00:00:09'],
            ['00:00:02:00', '00:00:01:00', '00:00:01:00'],
            ['00:00:10:00', '00:00:05:00', '00:00:05:00'],
            ['00:01:00:01', '00:01:00:00', '00:00:00:01'],
            ['01:01:01:01', '00:01:00:01', '01:00:01:00'],
        ];
    }

    public function subtractProvider()
    {
        return [
            ['00:00:00:00', '00:00:00:00', '00:00:00:00'],
            ['00:00:00:08', '00:00:00:09', '00:00:00:01'],
            ['00:00:00:00', '00:00:01:00', '00:00:01:00'],
            ['00:00:01:00', '00:00:02:00', '00:00:01:00'],
            ['00:00:00:00', '00:00:05:00', '00:00:05:00'],
            ['00:00:10:00', '00:00:15:00', '00:00:05:00'],
            ['00:00:59:23', '00:01:00:00', '00:00:00:01'],
        ];
    }

    public function getHoursProvider()
    {
        return [
            [1],
            [3],
            [6],
            [9],
            [10],
            [19],
        ];
    }

    public function invalidGetHoursProvider()
    {
        return [
            [-1],
            [-10],
            [25],
            [30],
            [1000],
        ];
    }

    public function getMinutesSecondsProvider()
    {
        return [
            [1],
            [3],
            [6],
            [9],
            [10],
            [19],
            [30],
            [55],
        ];
    }

    public function invalidGetMinutesSecondsProvider()
    {
        return [
            [-1],
            [-10],
            [65],
            [70],
            [1000],
        ];
    }


    public function getFramesProvider()
    {
        return [
            [1, FrameRate::FR_23_976, false],
            [23, FrameRate::FR_23_976, false],
            [1, FrameRate::FR_24, false],
            [23, FrameRate::FR_24, false],
            [1, FrameRate::FR_25, false],
            [24, FrameRate::FR_25, false],
            [1, FrameRate::FR_29_97, false],
            [29, FrameRate::FR_29_97, false],
            [1, FrameRate::FR_29_97, true],
            [29, FrameRate::FR_29_97, false],
            [1, FrameRate::FR_30, false],
            [29, FrameRate::FR_30, false],
        ];
    }

    // @TODO: add tests for FrameRate::FR_29_97 and dropframe
    public function invalidGetFramesProvider()
    {
        return [
            [-1, FrameRate::FR_23_976, false],
            [25, FrameRate::FR_23_976, false],
            [-1, FrameRate::FR_24, false],
            [25, FrameRate::FR_24, false],
            [-1, FrameRate::FR_25, false],
            [26, FrameRate::FR_25, false],
            [-1, FrameRate::FR_29_97, false],
            [30, FrameRate::FR_29_97, false],
            [-1, FrameRate::FR_30, false],
            [30, FrameRate::FR_30, false],
        ];
    }

    public function invalidFrameCountProvider()
    {
        return [
            [-1],
            [-10],
            [-20],
            [-25],
        ];
    }
}
