<?php

namespace FireworkWeb\Tests;

trait TimecodeProvider
{
    public function defaultFrameRateProvider()
    {
        return [
            [24000 / 1001],
            [25],
            [30000 / 1001],
            [30],
        ];
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
    public function toStringProvider()
    {
        return [
            // should properly return string
            ['00:00:00:00', 0, 24000 / 1001, false],
            ['00:00:00:00', 0, 24, false],
            ['00:00:00:00', 0, 25, false],
            ['00:00:00:00', 0, 30000 / 1001, false],
            ['00:00:00;00', 0, 30000 / 1001, true],
            ['00:00:00:00', 0, 30, false],
            // should properly return string
            ['00:00:20:20', 500, 24000 / 1001, false],
            ['00:00:20:20', 500, 24, false],
            ['00:00:20:00', 500, 25, false],
            ['00:00:16:20', 500, 30000 / 1001, false],
            ['00:00:16;20', 500, 30000 / 1001, true],
            ['00:00:16:20', 500, 30000 / 1001, false],
            // should properly return string
            ['00:06:56:16', 10000, 24000 / 1001, false],
            ['00:06:56:16', 10000, 24, false],
            ['00:06:40:00', 10000, 25, false],
            ['00:05:33:10', 10000, 30000 / 1001, false],
            ['00:05:33;20', 10000, 30000 / 1001, true],
            ['00:05:33:10', 10000, 30, false],
        ];
    }

    public function fromSecondsProvider()
    {
        return [
            // should properly return frame count (24000 / 1001 fps)
            [0, 0.041, 24000 / 1001],
            [1, 0.042, 24000 / 1001],
            [1, 0.083, 24000 / 1001],
            [2, 0.084, 24000 / 1001],
            [7201, 300.376, 24000 / 1001],
            [14403, 600.751, 24000 / 1001],
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
            // should properly return frame count (30000 / 1001 fps)
            [0, 0.033, 30000 / 1001],
            [1, 0.034, 30000 / 1001],
            [1, 0.066, 30000 / 1001],
            [2, 0.067, 30000 / 1001],
            [8991, 300, 30000 / 1001],
            [17982, 600, 30000 / 1001],
            // should properly return frame count (30 fps)
            [0, 0.033, 30],
            [1, 0.034, 30],
            [1, 0.066, 30],
            [2, 0.067, 30],
            [9000, 300, 30],
            [18000, 600, 30],
            //should properly return frame count (50 fps)
            [1, 0.033,50],
            [1, 0.034,50],
            [3, 0.066,50],
            [4, 0.082,50],
            [15000, 300, 50],
            [30000, 600, 50],
            //should properly return frame count (59.94 fps)
            [1, 0.033, 60000 / 1001],
            [2, 0.034, 60000 / 1001],
            [3, 0.066, 60000 / 1001],
            [4, 0.082, 60000 / 1001],
            [17982, 300, 60000 / 1001],
            [35964, 600, 60000 / 1001],
            //should properly return frame count (59.94 fps)
            [1, 0.033, 60],
            [2, 0.034, 60],
            [3, 0.066, 60],
            [4, 0.082, 60],
            [18000, 300, 60],
            [36000, 600, 60],
        ];
    }

    public function invalidFromSecondsProvider()
    {
        return [
            // should break with invalid seconds
            ['', 24000 / 1001, false],
            [false, 24, false],
            [new \DateTime(), 25, false],
            [null, 30000 / 1001, false],
            ['', 30000 / 1001, true],
            ['', 30, false],
            // should break with invalid framerate
            [1, 22, false],
            [1, 26, false],
            [1, 0, false],
            [1, 100, false],
            [1, 10000, false],
            // should break with invalid dropFrame
            [1, 24000 / 1001, true],
            [1, 24, true],
            [1, 25, true],
            [1, 30, true],
        ];
    }

    public function frameRateSupportedProvider()
    {
        return [
            [24000 / 1001, false],
            [24, false],
            [25, false],
            [30000 / 1001, false],
            [30, false],
            [50, false],
            [60000 / 1001, false],
            [60, false],
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
            [518, 12443, 24000 / 1001, false],
            [2597, '00:43:14:12', 24000 / 1001, false],
            [350, 8400, 24, false],
            [6190, '01:43:10:00', 24, false],
            [23, 576, 25, false],
            [1015, '00:16:55:24', 25, false],
            [83, 2500, 30000 / 1001, true],
            [3253, '00:54:13;25', 30000 / 1001, true],
            [210, 6323, 30, false],
            [10813, '03:00:13:27', 30, false],
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
            [1, 24000 / 1001, false],
            [23, 24000 / 1001, false],
            [1, 24, false],
            [23, 24, false],
            [1, 25, false],
            [24, 25, false],
            [1, 30000 / 1001, false],
            [29, 30000 / 1001, false],
            [1, 30000 / 1001, true],
            [29, 30000 / 1001, false],
            [1, 30, false],
            [29, 30, false],
        ];
    }

    // @TODO: add tests for 30000 / 1001 and dropframe
    public function invalidGetFramesProvider()
    {
        return [
            [-1, 24000 / 1001, false],
            [25, 24000 / 1001, false],
            [-1, 24, false],
            [25, 24, false],
            [-1, 25, false],
            [26, 25, false],
            [-1, 30000 / 1001, false],
            [30, 30000 / 1001, false],
            [-1, 30, false],
            [30, 30, false],
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
