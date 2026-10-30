<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    private const ORIGINS = [
        'Chinese', 'USA', 'Russian', 'Albanian', 'German', 'Japanese', 'French', 'Italian', 'Spanish', 'Latvian',
        'Polish', 'Swedish', 'Finnish', 'Korean', 'Indian', 'Brazilian', 'Canadian', 'British', 'Dutch', 'Turkish',
    ];

    private const TYPES = ['Software', 'Hardware'];

    public function definition(): array
    {
        $origin = fake()->randomElement(self::ORIGINS);
        $type = fake()->randomElement(self::TYPES);

        return [
            'name' => "$origin $type",
            'price' => fake()->randomFloat(2, 1, 999),
            'quantity' => fake()->numberBetween(0, 500),
            'description' => "$type made in $origin market.",
            'expiration_date' => fake()->optional(0.8)->dateTimeBetween('now', '+2 years'),
            'status' => fake()->randomElement(Product::STATUSES),
        ];
    }
}
