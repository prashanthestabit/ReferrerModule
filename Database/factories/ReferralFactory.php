<?php

namespace Modules\ReferrerModule\Database\factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ReferralFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\ReferrerModule\Entities\Referral::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'referrer_name'  => fake()->name,
            'referrer_email' => fake()->unique()->email,
            'referred_name'  => fake()->name,
            'referred_email' => fake()->unique()->email,
            'referral_code'  => Str::uuid()->toString(),
            'user_id'        => User::all()->random()->id,
            'status'         => 'pending',
            'created_at'     => fake()->dateTime(),
        ];

    }
}

