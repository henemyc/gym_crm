<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        $cities = ['Lucknow', 'Jaipur', 'Indore', 'Pune', 'Delhi', 'Noida', 'Kanpur', 'Bhopal', 'Mumbai'];

        return [
            'name' => fake()->name(),
            'gym_name' => fake()->company().' Fitness',
            'phone' => '9'.fake()->numerify('#########'),
            'location' => fake()->randomElement($cities),
            'source' => fake()->randomElement(Lead::SOURCES),
            'priority' => fake()->randomElement(array_keys(Lead::PRIORITIES)),
            'status' => fake()->randomElement(array_keys(Lead::STATUSES)),
            'notes' => fake()->optional(0.55)->sentence(),
            'assigned_to' => User::factory(),
            'created_by' => User::factory(),
        ];
    }
}
