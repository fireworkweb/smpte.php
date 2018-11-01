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
    const DEFAULT_FRAMERATE = 24.0;

    /**
     * @var bool
     */
    const DEFAULT_DROPFRAME = false;

    /**
     * @var int
     */
    const DROP_FRAME_CONCEPT = 17982;

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
     * Create a new Timecode
     *
     * @param int|string|DateTime $time
     * @param float $frameRate
     * @param bool $dropFrame
     * @return void
     */
    public function __construct(
        $time,
        float $frameRate = self::DEFAULT_FRAMERATE,
        bool $dropFrame = self::DEFAULT_DROPFRAME
    ) {
        if (! Validations::isValidTimeCode($time, $frameRate, $dropFrame)) {
            throw new \InvalidArgumentException('Invalid timecode');
        }

        $this->frameRate = $frameRate;
        $this->dropFrame = $dropFrame;

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
    public static function frameCountFromTimecode(
        $time,
        float $frameRate = self::DEFAULT_FRAMERATE,
        bool $dropFrame = self::DEFAULT_DROPFRAME
    ) : int {
        if (! Validations::isValidString($time, $frameRate, $dropFrame)) {
            throw new \InvalidArgumentException('Invalid string format');
        }

        $times = preg_split('/(:|;)/', $time);
        list($hours, $minutes, $seconds, $frames) = array_map(function ($time) {
            return (int) $time;
        }, $times);

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
    public static function fromSeconds($seconds, float $frameRate = self::DEFAULT_FRAMERATE) : self
    {
        if (! is_numeric($seconds)) {
            throw new \InvalidArgumentException('First argument must be a number');
        }

        return new self(intval($seconds * $frameRate), $frameRate);
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
    public function generateFrameCount($time) : self
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

        if ($frameCount === null) {
            throw new \InvalidArgumentException('Frame count can not be generated. Invalid timecode.');
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
        $drop = floor($this->frameCount / self::DROP_FRAME_CONCEPT);
        $mod = $this->frameCount % self::DROP_FRAME_CONCEPT;

        if ($mod < 2) {
            $mod += 2;
        }

        return $this->frameCount + ((18 * $drop) + (2 * (floor(($mod - 2) / self::DROP_FRAME_CONCEPT))));
    }

    /**
     * Get total of seconds based on frame count
     *
     * @return int
     */
    public function durationInSeconds() : int
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
     * @return void
     */
    public function setHours(int $hours) : void
    {
        if ($hours < 0 || $hours > 23) {
            throw new \InvalidArgumentException('The hours must be between 0 and 23');
        }

        $this->hours = $hours;
        $this->updateFramecount();
    }

    /**
     * @return int
     */
    public function getMinutes() : int
    {
        return $this->minutes;
    }

    /**
     * @param int $minuts
     * @return void
     */
    public function setMinutes(int $minuts) : void
    {
        if ($minuts < 0 || $minuts > 59) {
            throw new \InvalidArgumentException('The minuts must be between 0 and 59');
        }

        $this->minuts = $minuts;
        $this->updateFramecount();
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
     * @return void
     */
    public function setSeconds(int $seconds) : void
    {
        if ($seconds < 0 || $seconds > 59) {
            throw new \InvalidArgumentException('The seconds must be between 0 and 59');
        }

        $this->seconds = $seconds;
        $this->updateFramecount();
    }

    /**
     * @return int
     */
    public function getFrames() : int
    {
        return $this->frames;
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
    private function updateFramecount() : void
    {
        $this->frameCount = self::frameCountFromTimecode($this->toString(), $this->frameRate, $this->dropFrame);
    }
}
