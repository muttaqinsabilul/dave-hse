<?php

namespace Database\Factories;

use App\Models\HealthCheck;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HealthCheck>
 */
class HealthCheckFactory extends Factory
{
    protected $model = HealthCheck::class;

    public function definition(): array
    {
        return [
            'worker_id' => Worker::factory(),
            'tanggal' => today(),
            'sistol' => 118,
            'diastol' => 78,
            'suhu' => 36.6,
            'inspector_id' => fn (): string => User::inspectors()->inRandomOrder()->value('id')
                ?? User::create([
                    'id' => sprintf('INS-01-%03d', fake()->unique()->numberBetween(2, 999)),
                    'role' => 'inspector',
                    'site_code' => '01',
                    'name' => 'Inspector Cadangan',
                    'pin' => null,
                ])->id,
            'status' => 'NORMAL',
        ];
    }

    public function flagged(): static
    {
        return $this->state(fn (): array => ['sistol' => 150, 'diastol' => 98, 'suhu' => 38.2, 'status' => 'FLAG']);
    }
}
