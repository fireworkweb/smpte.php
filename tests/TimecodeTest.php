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

        $customTimecode = new Timecode(420, 30000 / 1001);

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
     * @dataProvider toStringProvider
     */
    public function testToString($expected, $time, $frameRate, $dropFrame)
    {
        $this->assertEquals($expected, (string) new Timecode($time, $frameRate, $dropFrame));
        $this->assertEquals($expected, (new Timecode($time, $frameRate, $dropFrame))->toString());
    }

    /**
     * @dataProvider fromSecondsProvider
     */
    public function testFromSeconds($expected, $time, $frameRate)
    {
        $this->assertEquals($expected, Timecode::fromSeconds($time, $frameRate)->getFrameCount());
    }

    /**
     * @dataProvider invalidFromSecondsProvider
     * @expectedException InvalidArgumentException
     */
    public function testInvalidFromSeconds($time, $frameRate, $dropFrame)
    {
        Timecode::fromSeconds($time, $frameRate, $dropFrame);
    }

    /**
     * @dataProvider durationInSecondsProvider
     */
    public function testDurationInSeconds($expected, $time, $frameRate, $dropFrame)
    {
        $this->assertEquals($expected, (new Timecode($time, $frameRate, $dropFrame))->durationInSeconds());
    }

    /**
     * @dataProvider addProvider
     */
    public function testAdd($expected, $time, $add)
    {
        $this->assertEquals($expected, (string) (new Timecode($time))->add($add));
    }

    /**
     * @dataProvider subtractProvider
     */
    public function testSubtract($expected, $time, $subtract)
    {
        $this->assertEquals($expected, (string) (new Timecode($time))->subtract($subtract));
    }

    /**
     * @dataProvider getHoursProvider
     */
    public function testGetHours($hours)
    {
        $this->assertEquals($hours, (new Timecode())->setHours($hours)->getHours());
    }

    /**
     * @dataProvider invalidGetHoursProvider
     * @expectedException InvalidArgumentException
     */
    public function testInvalidGetHours($hours)
    {
        (new Timecode())->setHours($hours);
    }

    /**
     * @dataProvider getMinutesSecondsProvider
     */
    public function testGetMinutes($minutes)
    {
        $this->assertEquals($minutes, (new Timecode())->setMinutes($minutes)->getMinutes());
    }

    /**
     * @dataProvider invalidGetMinutesSecondsProvider
     * @expectedException InvalidArgumentException
     */
    public function testInvalidGetMinutes($minutes)
    {
        (new Timecode())->setMinutes($minutes);
    }

    /**
     * @dataProvider getMinutesSecondsProvider
     */
    public function testGetSeconds($seconds)
    {
        $this->assertEquals($seconds, (new Timecode())->setSeconds($seconds)->getSeconds());
    }

    /**
     * @dataProvider invalidGetMinutesSecondsProvider
     * @expectedException InvalidArgumentException
     */
    public function testInvalidGetSeconds($seconds)
    {
        (new Timecode())->setSeconds($seconds);
    }

    /**
     * @dataProvider getFramesProvider
     */
    public function testGetFrames($frames, $framerate, $dropFrame)
    {
        $this->assertEquals($frames, (new Timecode(0, $framerate, $dropFrame))->setFrames($frames)->getFrames());
    }

    /**
     * @dataProvider invalidGetFramesProvider
     * @expectedException InvalidArgumentException
     */
    public function testInvalidGetFrames($frames, $framerate, $dropFrame, $minutes = 0)
    {
        (new Timecode(0, $framerate, $dropFrame))
            ->setMinutes($minutes)
            ->setFrames($frames);
    }

    /**
     * @dataProvider invalidFrameCountProvider
     * @expectedException InvalidArgumentException
     */
    public function testInvalidFrameCount($frameCount)
    {
        (new Timecode())->setFrameCount($frameCount);
    }
}
