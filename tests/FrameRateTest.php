<?php

namespace FireworkWeb\Tests;

use PHPUnit\Framework\TestCase;

class FrameRateTest extends TestCase
{
    use FrameRateProvider;

    /**
     * @dataProvider frameRateProvider
     */
    public function testConstantsValues($frameRate, $expected) {
        $this->assertEquals($frameRate, $expected);
    }

}
