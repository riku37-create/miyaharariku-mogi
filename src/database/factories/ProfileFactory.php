<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Profile;
use App\Models\User;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'post' => $this->faker->postcode,
            'image' => 'profile-img/person.png',
            'address' => $this->faker->address,
            'building' => $this->faker->secondaryAddress,
        ];
    }
}