<?php

namespace FireworkWeb\Tests;

use PHPUnit\Framework\TestCase;
use FireworkWeb\SMPTE\Timecode;
use FireworkWeb\SMPTE\Validations;

class TimecodeTest extends TestCase
{
    use TimecodeProvider;

    /**
     * @dataProvider frameRateSupportedProvider
     */
    public function testFrameRateSupported($frameRate, $dropFrame)
    {
        $this->assertTrue(Validations::isFrameRateSupported($frameRate, $dropFrame));
    }

    /**
     * @dataProvider frameRateNotSupportedProvider
     */
    public function testFrameRateNotSupported($frameRate, $dropFrame)
    {
        $this->assertFalse(Validations::isFrameRateSupported($frameRate, $dropFrame));
    }

    /**
     * @dataProvider validInstancesProvider
     */
    public function testValidInstances($time, $frameRate, $dropFrame)
    {
        $timecode = new Timecode($time, $frameRate, $dropFrame);
        $this->assertInstanceOf(Timecode::class, $timecode);
    }

    /**
     * @dataProvider invalidInstancesProvider
     * @expectedException InvalidArgumentException
     */
    public function testInvalidInstances($time, $frameRate, $dropFrame)
    {
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
}
