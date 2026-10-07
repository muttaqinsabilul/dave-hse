<?php

namespace Tests\Feature;

use App\Services\RiskLevelResolver;
use Database\Seeders\RiskMatrixSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiskMatrixTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([RiskMatrixSeeder::class]);
    }

    public function test_sel_matrix_sesuai_gambar(): void
    {
        $resolve = app(RiskLevelResolver::class);

        $this->assertSame(['skor' => 1, 'level' => 'LOW'], $resolve->resolve(1, 1));
        $this->assertSame(['skor' => 15, 'level' => 'EXTREME'], $resolve->resolve(3, 5));
        $this->assertSame(['skor' => 16, 'level' => 'HIGH'], $resolve->resolve(4, 4));
        $this->assertSame(['skor' => 25, 'level' => 'EXTREME'], $resolve->resolve(5, 5));
        $this->assertSame(['skor' => 6, 'level' => 'MEDIUM'], $resolve->resolve(2, 3));
    }
}
