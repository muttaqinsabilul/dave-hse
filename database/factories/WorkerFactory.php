<?php

namespace Database\Factories;

use App\Models\Worker;
use App\Services\WorkerIdGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Worker>
 */
class WorkerFactory extends Factory
{
    protected $model = Worker::class;

    public function definition(): array
    {
        return [
            'id' => fn (array $attributes): string => app(WorkerIdGenerator::class)->next($attributes['site_code'] ?? '01'),
            'site_code' => '01',
            'nama' => fake()->name(),
            'jenis_pekerjaan' => fake()->randomElement(['Tukang Las', 'Tukang Batu', 'Kenek', 'Operator Alat Berat']),
            'mandor_subkon' => fn (array $attributes): string => fake()->randomElement(config('hse.mandor_per_site.'.($attributes['site_code'] ?? '01'))),
            'foto_path' => 'workers/placeholder.png',
            'usia' => fake()->numberBetween(18, 55),
            'asal' => fake()->city(),
            'riwayat_penyakit' => null,
            'status_lokasi' => 'ONSITE',
            'tanggal_regis' => today()->subDays(fake()->numberBetween(1, 365)),
        ];
    }

    public function forSite(string $siteCode): static
    {
        return $this->state(fn (): array => ['site_code' => $siteCode]);
    }

    public function onsite(): static
    {
        return $this->state(fn (): array => ['status_lokasi' => 'ONSITE']);
    }

    public function offsite(): static
    {
        return $this->state(fn (): array => ['status_lokasi' => 'OFFSITE']);
    }
}
