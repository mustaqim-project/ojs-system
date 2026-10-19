<?php

namespace Database\Factories;

use App\Models\Journal;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Journal>
 */
class JournalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(4, true);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'abbreviation' => strtoupper(Str::substr($title, 0, 4)),
            'description' => fake()->sentence(),
            'issn_print' => fake()->numerify('####-####'),
            'issn_online' => fake()->numerify('####-####'),
            'publisher' => fake()->company(),
            'subject_area' => fake()->word(),
            'frequency' => fake()->randomElement(['monthly', 'bimonthly', 'quarterly', 'semiannual', 'annual']),
            'is_active' => true,
        ];
    }
}