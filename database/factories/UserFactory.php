<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = \App\Models\User::class;
    public function definition() { return [
        'name'=>$this->faker->name,'email'=>$this->faker->unique()->safeEmail,'password'=>Hash::make('password'),'phone'=>$this->faker->phoneNumber,'role'=>'customer'
    ]; }

    public function admin(){ return $this->state(['role'=>'admin']); }
    public function organizer(){ return $this->state(['role'=>'organizer']); }
}
