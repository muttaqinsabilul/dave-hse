<?php

namespace Database\Factories;

use App\Models\IbprReport;
use App\Models\User;
use App\Services\RiskLevelResolver;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IbprReport>
 */
class IbprReportFactory extends Factory
{
    protected $model = IbprReport::class;

    public function definition(): array
    {
        $likelihood = fake()->numberBetween(1, 5);
        $severity = fake()->numberBetween(1, 5);
        $resolved = app(RiskLevelResolver::class)->resolve($likelihood, $severity);

        return [
            'site_code' => '01',
            'tanggal_realtime' => now()->subHours(fake()->numberBetween(1, 72)),
            'kegiatan' => fake()->sentence(4),
            'bahaya' => fake()->sentence(6),
            'risiko' => fake()->sentence(6),
            'likelihood' => $likelihood,
            'severity' => $severity,
            'skor' => $resolved['skor'],
            'level' => $resolved['level'],
            'pengendalian' => fake()->sentence(8),
            'inspector_id' => fn (): string => User::inspectors()->inRandomOrder()->value('id')
                ?? User::create([
                    'id' => sprintf('INS-01-%03d', fake()->unique()->numberBetween(2, 999)),
                    'role' => 'inspector',
                    'site_code' => '01',
                    'name' => 'Inspector Cadangan',
                    'pin' => null,
                ])->id,
        ];
    }

    public function forSite(string $siteCode): static
    {
        return $this->state(fn (): array => ['site_code' => $siteCode]);
    }
}
