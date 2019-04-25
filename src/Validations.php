<?php

namespace FireworkWeb\SMPTE;

/**
 * Validations class representation
 */
class Validations
{
    /**
     * @var array
     */
    const SUPPORTED_FRAMERATES = [
        23.97,
        24,
        25,
        29.97,
        30,
    ];

     /**
     * Determine if is valid timecode
     *
     * @param int|string|DateTime $time
     * @param float $frameRate
     * @param bool $dropFrame
     * @return bool
     */
    public static function isValidTimeCode($time, float $frameRate, bool $dropFrame): bool
    {
        if (! self::isFrameRateSupported($frameRate, $dropFrame)) {
            throw new \InvalidArgumentException('Frame rate not supported');
        }

        return self::isValidInteger($time)
            || self::isValidDate($time)
            || self::isValidString($time, $frameRate, $dropFrame);
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

        list($hours, $minutes, $seconds, $frames) = preg_split('/(:|;)/', $value);

        if ($frames >= round($frameRate)) {
            return false;
        }

        if ($dropFrame && ($minutes % 10 !== 0 && $frames < 2 && $seconds === 0)) {
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
    public static function isFrameRateSupported(float $frameRate, bool $dropFrame) : bool
    {
        if ($frameRate !== 29.97 && $dropFrame) {
            throw new \InvalidArgumentException('Only 29.97 frame rate has drop frame support.');
        }

        return in_array($frameRate, self::SUPPORTED_FRAMERATES);
    }
}
