# smpte.php

[![Build Status](https://travis-ci.com/fireworkweb/smpte.php.svg?branch=master)](https://travis-ci.com/fireworkweb/smpte.php)
[![codecov](https://codecov.io/gh/fireworkweb/smpte.php/branch/master/graph/badge.svg)](https://codecov.io/gh/fireworkweb/smpte.php)

Easily deal with SMPTE Timecode format in PHP. If you need a Javascript lib, check out [fireworkweb/smpte.js](https://github.com/fireworkweb/smpte.js).

## Installation

You can install the package via composer:

```sh
composer require fireworkweb/smpte
```

## Usage

Include the Timecode or Validations classes:

```php
use FireworkWeb\SMPTE\Timecode;
use FireworkWeb\SMPTE\Validations;
```

You can instantiate it directly using new:

```php
// passing frame count
$timecode = new Timecode(360);

// passing a timecode string
$timecode = new Timecode('00:00:01:10');

// passing a Datetime object
$timecode = new Timecode(new \DateTime('01:34:12'));
```

Or you can use the static helper:

```php
$timecode = Timecode::fromSeconds(10);
```

### Properties

| Property              | Type  | Description                  |
| --------------------- | ----- | ---------------------------- |
| `getFrameCount()`     | `int` | Total number of frames       |
| `getHours()`          | `int` | Hours number                 |
| `getMinutes()`        | `int` | Minutes number               |
| `getSeconds()`        | `int` | Seconds number               |
| `getFrames()`         | `int` | Frames number                |
| `durationInSeconds()` | `int` | Timecode duration in seconds |

### Object Methods

#### `__construct($time = 0, $frameRate = null, $dropFrame = null)`

* `$time`: `int|String|Timecode` time to start with.
* `$frameRate`: `float` frame rate to calculate the timecode.
* `$dropFrame`: `bool` indicates if is drop frame. **ONLY WITH 29.97 FPS**

`$time` as int is the frame count to be setted with. To deal with seconds, use `fromSeconds`.

**Note:** if `$frameRate` or `$dropFrame` are null, it will use the default.

#### `toString()` / `__toString()`

Returns a timecode string representation.

```php
(new Timecode(360))->toString();
// "00:00:15:00"
```

#### `add($time, $operation = 1)`

Adds a timecode or a frame count to the current Timecode object.

* `$time`: `int|String|Timecode` indicating the value to be added.
* `$operation`: `int` used to get the sign of `time`.
* `return`: `Timecode` Reference to the `Timecode` object.

```php
$tc = new Timecode('00:01:00:00');

// Adding from string
$tc->add('00:00:30:00')->toString();
// 00:01:30:00

// Adding frame count
$tc->add(1)->toString();
// 00:01:30:01

// Adding from another object
$tc2 = new Timecode('00:01:00:00');
$tc->add($tc2)->toString();
// 00:02:30:01
```

#### `subtract($time)`

Substracts a timecode or a frame count to the current Timecode object.

* `$time`: `int|String|Timecode` indicating the value to be added.
* `return`: `Timecode` Reference to the `Timecode` object.

```php
$tc = new Timecode('00:03:00:00');

// Subtracting from string
$tc->subtract('00:00:30:00')->toString();
// 00:02:30:00

// Subtracting frame count
$tc->subtract(1)->toString();
// 00:02:29:23

// Subtracting from another object
$tc2 = new Timecode('00:01:00:00');
$tc->subtract($tc2)->toString();
// 00:01:29:23
```

#### `setHours($hours)`

Directly set object hours.

* `$hours`: `int` indicating the value to be setted.

```php
$tc = new Timecode('00:03:00:00');
$tc->setHours(1)->toString();
// 01:03:00:00
```

#### `setMinutes($minutes)`

Directly set object minutes.

* `$minutes`: `int` indicating the value to be setted.

```php
$tc = new Timecode('00:03:00:00');
$tc->setMinutes(1)->toString();
// 00:01:00:00
```

#### `setSeconds($seconds)`

Directly set object seconds.

* `$seconds`: `int` indicating the value to be setted.

```php
$tc = new Timecode('00:03:00:00');
$tc->setSeconds(1)->toString();
// 00:03:01:00
```

#### `setFrames($frames)`

Directly set object frames.

* `$frames`: `int` indicating the value to be setted.

```php
$tc = new Timecode('00:03:00:00');
$tc->setFrames(1)->toString();
// 00:03:00:01
```

#### `setFrameCount($frameCount)`

Directly set object frame count. This will recalculate all other attributes, so use it with care.

* `$frameCount`: `int` indicating the value to be setted.

```php
$tc = new Timecode('00:03:00:00');
$tc->setFrameCount(360)->toString();
// 00:00:15:00
```

### Static Methods

#### `frameCountFromTimecode($time, $frameRate = null, $dropFrame = null)`

Returns the frame count from a time.

* `$time`: `String` time as string to calculate.
* `$frameRate`: `float` frame rate to calculate the timecode.
* `$dropFrame`: `bool` indicates if is drop frame.
* `return`: `int` returns the frame count

#### `fromSeconds($seconds, $frameRate = null, $dropFrame = null)`

Instantiate a new object from seconds instead of timecode/framecount.

* `$seconds`: `int` seconds to convert
* `$frameRate`: `float` frame rate to calculate the timecode.
* `$dropFrame`: `bool` indicates if is drop frame.
* `return`: `Timecode` Returns the newly created object

```php
$tc = Timecode::fromSeconds(15);
$tc->toString();
// 00:00:15:00
```

#### `setDefaultFrameRate($frameRate)`

Change default frame rate to instantiate objects with.

* `$frameRate`: `float` New default frame rate.

```php
$tc = new Timecode();
$tc->getFrameRate();
// 24

Timecode::setDefaultFrameRate(25);

$tc2 = new Timecode();
$tc2->getFrameRate();
// 25
```

#### `setDefaultDropFrame($dropFrame)`

Change default drop frame to instantiate objects with.

* `$dropFrame`: `float` New default drop frame.

```php
$tc = new Timecode();
$tc->getDropFrame();
// false

Timecode::setDefaultDropFrame(true);

$tc2 = new Timecode();
$tc2->getDropFrame();
// true
```

## Contributing
All contribution is welcome, please feel free to open tickets and pull requests.

## License

[MIT.](LICENSE)