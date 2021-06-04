<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' =>  bcrypt('secret'), // password
            'remember_token' => Str::random(10),
            'verified' => $verificado = $this->faker->randomElement([User::USUARIO_VERIFICADO, User::USUARIO_NO_VERIFICADO]),
            'verification_token' => $verificado == User::USUARIO_VERIFICADO ? null : User::generarVerificationToken(),
            'admin' => $this->faker->randomElement(([User::USUARIO_ADMIN, User::USUARIO_REGULAR]))
        ];
    }
}
