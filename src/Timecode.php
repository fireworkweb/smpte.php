<?php

namespace FireworkWeb\SMPTE;

/**
 * Timecode class representation
 */
class Timecode
{
    /**
     * @var float
     */
    private $frameRate;

    /**
     * @var bool
     */
    private $dropFrame;

    /**
     * @var int
     */
    private $frameCount;

    /**
     * @var int
     */
    private $hours;

    /**
     * @var int
     */
    private $minutes;

    /**
     * @var int
     */
    private $seconds;

    /**
     * @var int
     */
    private $frames;

    /**
     * @var float
     */
    private static $defaultFrameRate = FrameRate::FR_24;

    /**
     * @var bool
     */
    private static $defaultDropFrame = false;

    /**
     * Create a new Timecode
     *
     * @param int|string|DateTime $time
     * @param float $frameRate
     * @param bool $dropFrame
     * @return void
     */
    public function __construct($time = 0, $frameRate = null, $dropFrame = null)
    {
        $this->frameRate = is_null($frameRate) ? self::$defaultFrameRate : $frameRate;
        $this->dropFrame = is_null($dropFrame) ? self::$defaultDropFrame : $dropFrame;

        if (! Validations::isValidTimeCode($time, $this->frameRate, $this->dropFrame)) {
            throw new \InvalidArgumentException('Invalid timecode');
        }

        $this->generateFrameCount($time);
    }

    /**
     * Convert the Timecode to its string representation.
     *
     * @return string
     */
    public function __toString() : string
    {
        return $this->toString();
    }

    /**
     * Calculate the frame count based on time code
     *
     * @param string $time
     * @param float $frameRate
     * @param bool $dropFrame
     * @return int
     */
    public static function frameCountFromTimecode($time, $frameRate = null, $dropFrame = null) : int
    {
        $frameRate = is_null($frameRate) ? self::$defaultFrameRate : $frameRate;
        $dropFrame = is_null($dropFrame) ? self::$defaultDropFrame : $dropFrame;

        if (! Validations::isValidString($time, $frameRate, $dropFrame)) {
            throw new \InvalidArgumentException('Invalid string format');
        }

        $times = preg_split('/(:|;)/', $time);
        list($hours, $minutes, $seconds, $frames) = array_map('intval', $times);

        $roundFrameRate = round($frameRate);
        $frameCount = self::calculateFrameCount($roundFrameRate, $hours, $minutes, $seconds, $frames);

        if ($dropFrame) {
            $totalMinutes = (60 * $hours) + $minutes;

            return (int) ($frameCount - (2 * ($totalMinutes - floor($totalMinutes / 10))));
        }

        return (int) $frameCount;
    }

    /**
     * Create a new Timecode of a given seconds
     *
     * @param int $seconds
     * @param float $frameRate
     * @param bool $dropFrame
     * @return self
     */
    public static function fromSeconds($seconds, $frameRate = null, $dropFrame = null) : self
    {
        $frameRate = is_null($frameRate) ? self::$defaultFrameRate : $frameRate;
        $dropFrame = is_null($dropFrame) ? self::$defaultDropFrame : $dropFrame;

        if (! is_numeric($seconds)) {
            throw new \InvalidArgumentException('First argument must be a number');
        }

        if (! Validations::isFrameRateSupported($frameRate, $dropFrame)) {
            throw new \InvalidArgumentException('Frame Rate not supported');
        }

        return new self(intval($seconds * $frameRate), $frameRate, $dropFrame);
    }

    /**
     * Calculate frame count
     *
     * @param float $frameRate
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     * @param int $frames
     * @return float
     */
    public static function calculateFrameCount(
        float $frameRate,
        int $hours,
        int $minutes,
        int $seconds,
        int $frames
    ) : float {
        return ($frameRate * 60 * 60 * $hours)
            + ($frameRate * 60 * $minutes)
            + ($frameRate * $seconds)
            + $frames;
    }

    /**
     * @param float $frameRate
     */
    public static function setDefaultFrameRate(float $frameRate)
    {
        self::$defaultFrameRate = $frameRate;
    }

    /**
     * @param float $frameRate
     */
    public static function setDefaultDropFrame(bool $dropFrame)
    {
        self::$defaultDropFrame = $dropFrame;
    }

    /**
     * Convert the Timecode to its string representation.
     *
     * @return string
     */
    public function toString() : string
    {
        $dropFrameSymbol = $this->dropFrame ? ';' : ':';

        return sprintf("%02d:%02d:%02d{$dropFrameSymbol}%02d", $this->hours, $this->minutes, $this->seconds, $this->frames);
    }

    /**
     * Create a frame count based on your time type
     *
     * @param int|string|DateTime $time
     * @return self
     */
    private function generateFrameCount($time) : self
    {
        $frameCount = null;

        if (is_integer($time)) {
            $frameCount = (int) $time;
        } elseif ($time instanceof \DateTime) {
            $midnight = clone $time;
            $midnight->setTime(0, 0);

            $frameCount = (int) (($time->getTimestamp() - $midnight->getTimestamp()) * $this->frameRate);
        } elseif (is_string($time)) {
            $frameCount = (int) self::frameCountFromTimecode($time, $this->frameRate, $this->dropFrame);
        }

        $this->setFrameCount($frameCount);

        return $this;
    }

    /**
     * Calculate frame count based on drop frame concept
     *
     * @return float
     */
    public function calculateFrameCountWithDropFrame() : float
    {
        $drop = floor($this->frameCount / 17982);
        $mod = $this->frameCount % 17982;

        if ($mod < 2) {
            $mod += 2;
        }

        return $this->frameCount + ((18 * $drop) + (2 * (floor(($mod - 2) / 1798))));
    }

    /**
     * Get total duration in seconds based on frame count, rounded down to the nearest second
     *
     * @return int
     */
    public function durationInSeconds() : int
    {
        return (int) ($this->frameCount / $this->frameRate);
    }

    /**
     * Get total duration in seconds based on frame count, rounded to the nearest second
     *
     * @return int
     */
    public function durationInSecondsRounded() : int
    {
        return (int) round($this->frameCount / $this->frameRate);
    }

    /**
     * Get total duration in seconds based on frame count, rounded to the nearest second
     * Ensures a minimum of 1 second if frames are present
     *
     * @return int
     */
    public function durationInSecondsRoundedMinOne() : int
    {
        if ($this->frameCount === 0) {
            return 0;
        }

        return max(1, (int) round($this->frameCount / $this->frameRate));
    }

    /**
     * Get total duration in seconds based on frame count, rounded up to the nearest second
     *
     * @return int
     */
    public function durationInSecondsRoundedUp() : int
    {
        return (int) ceil($this->frameCount / $this->frameRate);
    }

    /**
     * Get total duration in seconds with fractional precision based on frame count
     *
     * @return float
     */
    public function durationInSecondsWithFractions() : float
    {
        return $this->frameCount / $this->frameRate;
    }

    /**
     * Adds a timecode or a frame count to the current SMPTE object
     *
     * @param int|string|DateTime $time
     * @param int $operation
     * @return self
     */
    public function add($time, int $operation = 1) : self
    {
        $operation = $operation < 0 ? -1 : 1;
        $timeCode = new self($time, $this->getFrameRate(), $this->getDropFrame());
        $frameCount = $this->getFrameCount() + ($timeCode->getFrameCount() * $operation);

        $this->setFrameCount($frameCount);

        return $this;
    }

    /**
     * Subtract a timecode or a frame count to the current SMPTE object
     *
     * @param int|string|DateTime $time
     * @return self
     */
    public function subtract($time) : self
    {
        return $this->add($time, -1);
    }

    /**
     * @return int
     */
    public function getHours() : int
    {
        return $this->hours;
    }

    /**
     * @param int $hours
     * @return self
     */
    public function setHours(int $hours) : self
    {
        if ($hours < 0 || $hours > 23) {
            throw new \InvalidArgumentException('The hours must be between 0 and 23');
        }

        $this->hours = $hours;
        $this->updateFramecount();

        return $this;
    }

    /**
     * @return int
     */
    public function getMinutes() : int
    {
        return $this->minutes;
    }

    /**
     * @param int $minutes
     * @return self
     */
    public function setMinutes(int $minutes) : self
    {
        if ($minutes < 0 || $minutes > 59) {
            throw new \InvalidArgumentException('The minutes must be between 0 and 59');
        }

        $this->minutes = $minutes;
        $this->updateFramecount();

        return $this;
    }

    /**
     * @return int
     */
    public function getSeconds() : int
    {
        return $this->seconds;
    }

    /**
     * @param int $seconds
     * @return self
     */
    public function setSeconds(int $seconds) : self
    {
        if ($seconds < 0 || $seconds > 59) {
            throw new \InvalidArgumentException('The seconds must be between 0 and 59');
        }

        $this->seconds = $seconds;
        $this->updateFramecount();

        return $this;
    }

    /**
     * @return int
     */
    public function getFrames() : int
    {
        return $this->frames;
    }

    /**
     * @param int $frames
     * @return self
     */
    public function setFrames(int $frames) : self
    {
        if ($frames < 0 || $frames >= ceil($this->frameRate)) {
            throw new \InvalidArgumentException('The frames must be between 0 and the framerate');
        }

        if ($this->dropFrame
            && $this->minutes % 10 !== 0
            && $this->seconds === 0
            && $frames < 2
        ) {
            throw new \InvalidArgumentException('The frames must not be less than 2 when dropframe and minutes multiple by 10');
        }

        $this->frames = $frames;
        $this->updateFramecount();

        return $this;
    }

    /**
     * @return float
     */
    public function getFrameRate() : float
    {
        return $this->frameRate;
    }

    /**
     * @return bool
     */
    public function getDropFrame() : bool
    {
        return $this->dropFrame;
    }

    /**
     * @return int
     */
    public function getFrameCount() : int
    {
        return $this->frameCount;
    }

    /**
     * Set a new value to frame count
     * Update time code
     *
     * @param int $frameCount
     * @return self
     */
    public function setFrameCount(int $frameCount) : self
    {
        if ($frameCount < 0) {
            throw new \InvalidArgumentException('Frame count can not be negative');
        }

        $this->frameCount = (int) $frameCount;

        $recalculatedFrameCount = $this->dropFrame ? $this->calculateFrameCountWithDropFrame() : $this->frameCount;
        $frameRate = round($this->frameRate);

        $this->hours = (int) (floor($recalculatedFrameCount / ($frameRate * 3600)) % 24);
        $this->minutes = (int) (floor($recalculatedFrameCount / ($frameRate * 60)) % 60);
        $this->seconds = (int) (floor($recalculatedFrameCount / $frameRate) % 60);
        $this->frames = (int) ($recalculatedFrameCount % $frameRate);

        return $this;
    }

    /**
     * Update the frame count based on the current time code
     *
     * @return void
     */
    private function updateFramecount()
    {
        $this->frameCount = self::frameCountFromTimecode($this->toString(), $this->frameRate, $this->dropFrame);
    }
}
