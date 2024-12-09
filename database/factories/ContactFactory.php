<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contact;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{

    protected $model = Contact::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),  
            'name' => $this->faker->name,  
            'cpf' => $this->faker->unique()->numerify('###########'), 
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Contact $contact) {
            $contact->phones()->createMany([
                ['phone' => $this->faker->phoneNumber],
                ['phone' => $this->faker->phoneNumber],
            ]);

            $contact->addresses()->createMany([
                [
                    'address' => $this->faker->streetAddress,
                    'number' => $this->faker->buildingNumber,
                    'cep' => $this->faker->postcode,
                    'latitude' => $this->faker->latitude,
                    'longitude' => $this->faker->longitude,
                ],
                [
                    'address' => $this->faker->streetAddress,
                    'number' => $this->faker->buildingNumber,
                    'cep' => $this->faker->postcode,
                    'latitude' => $this->faker->latitude,
                    'longitude' => $this->faker->longitude,
                ]
            ]);
        });
    }
}
