<?php

namespace FireworkWeb\Tests;

trait TimecodeProvider
{
    public function defaultFrameRateProvider()
    {
        return [
            [23.97],
            [25],
            [29.97],
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
            ['00:00:00:00', 0, 23.97, false],
            ['00:00:00:00', 0, 24, false],
            ['00:00:00:00', 0, 25, false],
            ['00:00:00:00', 0, 29.97, false],
            ['00:00:00;00', 0, 29.97, true],
            ['00:00:00:00', 0, 30, false],
            // should properly return string
            ['00:00:20:20', 500, 23.97, false],
            ['00:00:20:20', 500, 24, false],
            ['00:00:20:00', 500, 25, false],
            ['00:00:16:20', 500, 29.97, false],
            ['00:00:16;20', 500, 29.97, true],
            ['00:00:16:20', 500, 29.97, false],
            // should properly return string
            ['00:06:56:16', 10000, 23.97, false],
            ['00:06:56:16', 10000, 24, false],
            ['00:06:40:00', 10000, 25, false],
            ['00:05:33:10', 10000, 29.97, false],
            ['00:05:33;20', 10000, 29.97, true],
            ['00:05:33:10', 10000, 30, false],
        ];
    }

    public function fromSecondsProvider()
    {
        return [
            // should properly return frame count (23.97 fps)
            [0, 0.041, 23.97],
            [1, 0.042, 23.97],
            [1, 0.083, 23.97],
            [2, 0.084, 23.97],
            [7200, 300.301, 23.97],
            [14400, 600.601, 23.97],
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

    public function invalidFromSecondsProvider()
    {
        return [
            // should break with invalid seconds
            ['', 23.97, false],
            [false, 24, false],
            [new \DateTime(), 25, false],
            [null, 29.97, false],
            ['', 29.97, true],
            ['', 30, false],
            // should break with invalid framerate
            [1, 22, false],
            [1, 26, false],
            [1, 0, false],
            [1, 50, false],
            [1, 100, false],
            [1, 10000, false],
            // should break with invalid dropFrame
            [1, 23.97, true],
            [1, 24, true],
            [1, 25, true],
            [1, 30, true],
        ];
    }

    public function frameRateSupportedProvider()
    {
        return [
            [23.97, false],
            [24, false],
            [25, false],
            [29.97, false],
            [30, false],
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
            [518, 12443, 23.97, false],
            [2597, '00:43:14:12', 23.97, false],
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
            [1, 23.97, false],
            [23, 23.97, false],
            [1, 24, false],
            [23, 24, false],
            [1, 25, false],
            [24, 25, false],
            [1, 29.97, false],
            [29, 29.97, false],
            [1, 29.97, true],
            [29, 29.97, false],
            [1, 30, false],
            [29, 30, false],
        ];
    }

    // @TODO: add tests for 29.97 and dropframe
    public function invalidGetFramesProvider()
    {
        return [
            [-1, 23.97, false],
            [25, 23.97, false],
            [-1, 24, false],
            [25, 24, false],
            [-1, 25, false],
            [26, 25, false],
            [-1, 29.97, false],
            [30, 29.97, false],
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
