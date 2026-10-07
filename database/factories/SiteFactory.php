<?php

namespace Database\Factories;

use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Site>
 */
class SiteFactory extends Factory
{
    protected $model = Site::class;

    public function definition(): array
    {
        return [
            'code' => str_pad((string) fake()->unique()->numberBetween(6, 99), 2, '0', STR_PAD_LEFT),
            'name' => fake()->city(),
        ];
    }
}
