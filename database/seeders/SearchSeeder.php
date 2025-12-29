<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'test')->first();
        for ($i = 0; $i < 20; $i++) {
            Contact::create([
                "first_name" => "first_test" . $i,
                "last_name" => "last_test" . $i,
                "email" => "email_test" . $i,
                "phone" => "11111" . $i,
                "user_id" => $user->id
            ]);
        }
    }
}
