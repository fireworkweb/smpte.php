<?php

namespace FireworkWeb\Tests;

use FireworkWeb\SMPTE\FrameRate;

trait FrameRateProvider
{
    public function frameRateProvider()
    {
        return [
            [FrameRate::FR_23_976, 24000 / 1001],
            [FrameRate::FR_24, 24],
            [FrameRate::FR_25, 25],
            [FrameRate::FR_29_97, 30000 / 1001],
            [FrameRate::FR_30, 30],
            [FrameRate::FR_50, 50],
            [FrameRate::FR_59_94, 60000 / 1001],
            [FrameRate::FR_60, 60],
        ];
    }
}
