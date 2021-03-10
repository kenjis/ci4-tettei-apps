<?php

declare(strict_types=1);

namespace App\Libraries;

use CodeIgniter\Test\CIUnitTestCase;

class SystemClockTest extends CIUnitTestCase
{
    public function test_インスタンス化できる(): void
    {
        $clock = new SystemClock();

        $this->assertInstanceOf(SystemClock::class, $clock);
    }

    public function test_現在時刻を取得できる(): void
    {
        $clock = new SystemClock();

        $this->assertTrue(
            '2021-03-10 00:00:00' < $clock->now()->format('Y-m-d H:i:s')
        );
    }

    public function test_現在時刻をテスト用に固定できる(): void
    {
        $clock = new SystemClock();
        $clock->freeze('1999-01-01 00:00:00');

        $this->assertSame(
            '1999-01-01 00:00:00',
            $clock->now()->format('Y-m-d H:i:s')
        );
    }
}
