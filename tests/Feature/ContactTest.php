<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function testCreateSuccess(){
        $this->seed([UserSeeder::class]);
        $this->post('/api/contacts', [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test',
            'phone' => 'test',
        ], [
            "Authorization" => "test"
        ])->assertStatus(201)
        ->assertJson([
            "data" => [
                'first_name' => 'test',
                'last_name' => 'test',
                'email' => 'test',
                'phone' => 'test',
            ]
        ]);
    }
    public function testCreateFail(){
        $this->seed([UserSeeder::class]);
        $this->post('/api/contacts', [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'phone' => '',
        ], [
            "Authorization" => "test"
        ])->assertStatus(400)
        ->assertJson([
            "errors" => [
                'first_name' => ['The first name field is required.'],
                'last_name' => ['The last name field is required.'],
                'email' => ['The email field is required.'],
                'phone' => ['The phone field is required.'],
            ]
        ]);
    }
    public function testGetContactSuccess(){
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact_id = Contact::where('first_name', 'test')->first()->id;
        $this->get("/api/contacts/$contact_id", [
            "Authorization" => "test"
        ])->assertStatus(200)
        ->assertJson([
            "data" => [
                'id' => $contact_id,
                'first_name' => 'test',
                'last_name' => 'test',
                'email' => 'test',
                'phone' => 'test',
            ]
        ]);
    }
    public function testGetContactNotFound(){
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact_id = Contact::where('first_name', 'test')->first()->id;
        $this->get("/api/contacts/".$contact_id+1, [
            "Authorization" => "test"
        ])->assertStatus(404)
        ->assertJson([
            "errors" => [
                "message" => [
                    "Contact not found"
                ]
            ]
        ]);
    }
    public function testGetOtherUserContact(){
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact_id = Contact::where('first_name', 'test')->first()->id;
        $this->get("/api/contacts/$contact_id", [
            "Authorization" => "test2"
        ])->assertStatus(404)
        ->assertJson([
            "errors" => [
                "message" => [
                    "Contact not found"
                ]
            ]
        ]);
    }
    public function testUpdateSuccess(){
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact_id = Contact::where('first_name', 'test')->first()->id;
        $this->put('/api/contacts/'.$contact_id, [
            "first_name" => "apa aja lah",
            "last_name" => "apa aja lah",
            "email" => "apa aja lah",
            "phone" => "apa aja lah",
        ], [
            "Authorization" => "test",
        ])->assertStatus(200)
        ->assertJson([
            "data" => [
                "id" => $contact_id,
                "first_name" => "apa aja lah",
                "last_name" => "apa aja lah",
                "email" => "apa aja lah",
                "phone" => "apa aja lah",
            ]
        ]);
    }
    public function testUpdateError(){
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact_id = Contact::where('first_name', 'test')->first()->id;
        $this->put('/api/contacts/'.$contact_id, [
            "first_name" => "",
            "last_name" => "",
            "email" => "",
            "phone" => "",
        ], [
            "Authorization" => "test",
        ])->assertStatus(400)
        ->assertJson([
            "errors" => [
                "first_name" => ["The first name field is required."],
                "last_name" => ["The last name field is required."],
                "email" => ["The email field is required."],
                "phone" => ["The phone field is required."]
            ]
        ]);
    }
    public function testContactDeleteSuccess(){
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact_id = Contact::where('first_name', 'test')->first()->id;
        $this->delete('/api/contacts/'.$contact_id, [], [
            "Authorization" => "test",
        ])->assertStatus(200)
        ->assertJson([
            "data" => true
        ]);
    }
    public function testContactDeleteNotFound(){
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact_id = Contact::where('first_name', 'test')->first()->id;
        $this->delete('/api/contacts/'.$contact_id + 1, [], [
            "Authorization" => "test",
        ])->assertStatus(404)
        ->assertJson([
            "errors" => [
                "message" => [
                    "Contact not found"
                ]
            ]
        ]);
    }
    public function testSearchByFirstName(){
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?name=first_test', [
            "Authorization" => "test"
        ])
        ->assertStatus(200)->json();
        
        Log::info($response);
        $this->assertEquals(10, count($response['data']));
        $this->assertEquals(20, $response['meta']['total']);
    }
    public function testSearchByLastName(){
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?name=last_test', [
            "Authorization" => "test"
            ])
            ->assertStatus(200)->json();
        
        Log::info($response);
        $this->assertEquals(10, count($response['data']));
        $this->assertEquals(20, $response['meta']['total']);
    }
    public function testSearchByEmail(){
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?email=email_test', [
            "Authorization" => "test"
        ])
        ->assertStatus(200)->json();
        
        Log::info($response);
        $this->assertEquals(10, count($response['data']));
        $this->assertEquals(20, $response['meta']['total']);

    }
    public function testSearchByPhone(){
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?phone=11111', [
            "Authorization" => "test"
        ])
        ->assertStatus(200)->json();
        
        Log::info($response);
        $this->assertEquals(10, count($response['data']));
        $this->assertEquals(20, $response['meta']['total']);
    }
    public function testSearchNotFound(){
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?name=11111', [
            "Authorization" => "test"
        ])
        ->assertStatus(200)
        ->json();
        
        Log::info($response);
        $this->assertEquals(0, count($response['data']));
        $this->assertEquals(0, $response['meta']['total']);
    }
    public function testSearchWithPage(){
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?page=2&size=5', [
            "Authorization" => "test"
        ])
        ->assertStatus(200)
        ->json();
        
        Log::info($response);
        $this->assertEquals(5, count($response['data']));
        $this->assertEquals(20, $response['meta']['total']);
        $this->assertEquals(2, $response['meta']['current_page']);
        $this->assertEquals(4, $response['meta']['last_page']);
    }
}
