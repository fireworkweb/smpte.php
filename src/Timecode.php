<?php

namespace FireWorkWeb\SMPTE;

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
     * @var array
     */
    const SUPPORTED_FRAMERATES = [
        23.976,
        24,
        25,
        29.97,
        30,
    ];

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
    public function __construct($time, float $frameRate = self::DEFAULT_FRAMERATE, bool $dropFrame = self::DEFAULT_DROPFRAME)
    {
        if (! self::isValidTimeCode($time, $frameRate, $dropFrame)) {
            throw new \InvalidArgumentException('Invalid timecode');
        }

        $this->frameRate = $frameRate;
        $this->dropFrame = $dropFrame;

        $this->generateFrameCount($time);
    }

    /**
     * Determine if is valid timecode
     *
     * @param int|string|DateTime $time
     * @param float $frameRate
     * @param bool $dropFrame
     * @return self
     */
    public static function isValidTimeCode($time, float $frameRate, bool $dropFrame)
    {
        if (! self::isFrameRateSupported($frameRate, $dropFrame)) {
            throw new \InvalidArgumentException('Frame rate not supported');
        }

        return self::isValidInteger($time) || self::isValidDate($time) || self::isValidString($time, $frameRate, $dropFrame);
    }

    /**
     * Determine if timecode of the type integer is valid
     *
     * @param int $value
     * @return bool
     */
    public static function isValidInteger($value) : bool
    {
        if (! is_integer($value)) {
            return false;
        }

        if ($value < 0) {
            throw new \InvalidArgumentException('Negative frames not supported');
        }

        return true;
    }

    /**
     * Determine if timecode is intance of DateTime
     *
     * @param DateTime $value
     * @return bool
     */
    public static function isValidDate($value) : bool
    {
        return $value instanceof \DateTime;
    }

    /**
     * Determine if timecode of the type string is valid
     *
     * @param string $value
     * @param float $frameRate
     * @param bool $dropFrame
     * @return bool
     */
    public static function isValidString($value, float $frameRate, bool $dropFrame) : bool
    {
        if (! is_string($value)) {
            return false;
        }

        if (! $dropFrame && strpos($value, ';') !== false) {
            return false;
        }

        if ($dropFrame && $value[8] !== ';') {
            return false;
        }

        // hh:mm:ss:ff
        $timeCodeFormat = '/^(?:[0-1][0-9]|2[0-3])(:|;)(?:[0-5][0-9])\1(?:[0-5][0-9])(:|;)(?:[0-2][0-9])$/';
        if (! preg_match($timeCodeFormat, $value)) {
            return false;
        }

        $parts = preg_split('/(:|;)/', $value);

        if ($parts[3] >= round($frameRate)) {
            return false;
        }

        if ($dropFrame && ($parts[1] % 10 !== 0 && $parts[3] < 2 && $parts[2] === 0)) {
            return false;
        }

        return true;
    }

    /**
     * Determine if frame rate is supported
     *
     * @param float $frameRate
     * @param bool $dropFrame
     * @return bool
     */
    public static function isFrameRateSupported(float $frameRate, bool $dropFrame = self::DEFAULT_DROPFRAME) : bool
    {
        if ($frameRate !== 29.97 && $dropFrame) {
            throw new \InvalidArgumentException('Only 29.97 frame rate has drop frame support.');
        }

        return in_array($frameRate, self::SUPPORTED_FRAMERATES);
    }

    /**
     * Calculate the frame count based on time code
     *
     * @param string $time
     * @param float $frameRate
     * @param bool $dropFrame
     * @return int
     */
    public static function frameCountFromTimecode($time, float $frameRate = self::DEFAULT_FRAMERATE, bool $dropFrame = self::DEFAULT_DROPFRAME) : int
    {
        if (! self::isValidString($time, $frameRate, $dropFrame)) {
            throw new \InvalidArgumentException('Invalid string format');
        }

        $parts = preg_split('/(:|;)/', $time);

        $roundFrameRate = round($frameRate);
        $frameCount = ($roundFrameRate * 60 * 60 * $parts[0])
            + ($roundFrameRate * 60 * $parts[1])
            + ($roundFrameRate * $parts[2])
            + $parts[3];

        if ($dropFrame) {
            $totalMinutes = (60 * $parts[0]) + $parts[1];

            return intval($frameCount - (2 * ($totalMinutes - floor($totalMinutes / 10))));
        }

        return intval($frameCount);
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
     * @return void
     */
    public function generateFrameCount($time)
    {
        $frameCount = null;

        if (is_integer($time)) {
            $frameCount = intval($time);
        } else if ($time instanceof \DateTime) {
            $midnight = clone $time;
            $midnight->setTime(0, 0);

            $frameCount = intval(($time->getTimestamp() - $midnight->getTimestamp()) * $this->frameRate);
        } else if (is_string($time)) {
            $frameCount = intval(self::frameCountFromTimecode($time, $this->frameRate, $this->dropFrame));
        }

        if ($frameCount !== null) {
            $this->setFrameCount($frameCount);

            return;
        }

        throw new \InvalidArgumentException('Frame count can not be generated. Invalid timecode.');
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
     * Update the frame count based on the current time code
     *
     * @return void
     */
    public function updateFramecount() : void
    {
        $this->frameCount = self::frameCountFromTimecode($this->toString(), $this->frameRate, $this->dropFrame);
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
        $operation = $operation < 0
            ? -1
            : 1;

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
    public function setMinuts(int $minuts) : void
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
     * @return void
     */
    public function setFrameCount(int $frameCount)
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
    }
}
