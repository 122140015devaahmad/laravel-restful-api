<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        $user = User::where('username', 'test')->first();
        Contact::create([
            "first_name" => "test",
            "last_name" => "test",
            "email" => "test",
            "phone" => "test",
            "user_id" => $user->id
        ]);
        $user = User::where('username', 'test2')->first();
        Contact::create([
            "first_name" => "test2",
            "last_name" => "test2",
            "email" => "test2",
            "phone" => "test2",
            "user_id" => $user->id
        ]);
        $user = User::where('username', 'test3')->first();
        Contact::create([
            "first_name" => "test3",
            "last_name" => "test3",
            "email" => "test3",
            "phone" => "test3",
            "user_id" => $user->id
        ]);
    }
}
