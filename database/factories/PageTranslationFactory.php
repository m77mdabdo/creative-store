<?php

namespace Database\Factories;

use App\Models\PageTranslation;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageTranslationFactory extends Factory
{
    protected $model = PageTranslation::class;

    public function definition(): array
    {
        return [
            'page_id' => Page::query()->inRandomOrder()->value('id') ?? Page::factory(),
            'locale' => $this->faker->randomElement(['ar','en']),
            'title' => $this->faker->sentence(4),
            'content' => $this->faker->paragraphs(3, true),
        ];
    }
}
