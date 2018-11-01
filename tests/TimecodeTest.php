<?php

namespace FireworkWeb\Tests;

use PHPUnit\Framework\TestCase;
use FireworkWeb\SMPTE\Timecode;
use FireworkWeb\SMPTE\Validations;

class TimecodeTest extends TestCase
{
    use TimecodeProvider;

    /**
     * @dataProvider defaultFrameRateProvider
     */
    public function testDefaultFrameRate($frameRate)
    {
        $timecode = new Timecode(360);

        Timecode::setDefaultFrameRate($frameRate);

        $customTimecode = new Timecode(420);

        $this->assertEquals(24, $timecode->getFrameRate());
        $this->assertEquals($frameRate, $customTimecode->getFrameRate());

        Timecode::setDefaultFrameRate(24);
    }

    public function testDefaultDropFrame()
    {
        $timecode = new Timecode(360);

        Timecode::setDefaultDropFrame(true);

        $customTimecode = new Timecode(420, 29.97);

        $this->assertEquals(false, $timecode->getDropFrame());
        $this->assertEquals(true, $customTimecode->getDropFrame());

        Timecode::setDefaultDropFrame(false);
    }

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
