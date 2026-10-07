<?php

namespace Tests\Feature;

use App\Services\HealthStatusResolver;
use Tests\TestCase;

class HealthStatusTest extends TestCase
{
    public function test_ambang_normal_dan_flag(): void
    {
        $resolve = app(HealthStatusResolver::class);

        $this->assertSame('NORMAL', $resolve->resolve(120, 80, 36.8));
        $this->assertSame('NORMAL', $resolve->resolve(90, 60, 36.5));
        $this->assertSame('FLAG', $resolve->resolve(145, 95, 36.8));
        $this->assertSame('FLAG', $resolve->resolve(120, 80, 38.5));
        $this->assertSame('FLAG', $resolve->resolve(85, 75, 36.8));
    }
}
