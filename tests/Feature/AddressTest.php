<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function testCreateSuccess(){
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::where('first_name', 'test')->first()->id;

        $this->post('/api/contacts/'.$contact.'/addresses', [
            "street" => "test",
            "city" => "test",
            "province" => "test",
            "country" => "test",
            "postal_code" => "test"
        ], [
            "Authorization" => "test"
        ])->assertStatus(201)
        ->assertJson([
            "data" => [
                "street" => "test",
                "city" => "test",
                "province" => "test",
                "country" => "test",
                "postal_code" => "test",
            ]
        ]);
    }
    public function testCreateFailed(){
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::where('first_name', 'test')->first()->id;

        $this->post('/api/contacts/'.$contact.'/addresses', [
            "street" => "",
            "city" => "",
            "province" => "",
            "country" => "",
            "postal_code" => ""
        ], [
            "Authorization" => "test"
        ])->assertStatus(400)
        ->assertJson([
            "errors" => [
                "street" => ["The street field is required."],
                "city" => ["The city field is required."],
                "province" => ["The province field is required."],
                "country" => ["The country field is required."],
                "postal_code" => ["The postal code field is required."],
            ]
        ]);
    }
    public function testCreateContactNotFound(){
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::where('first_name', 'test')->first()->id;

        $this->post('/api/contacts/'.$contact + 1 .'/addresses', [
            "street" => "test",
            "city" => "test",
            "province" => "test",
            "country" => "test",
            "postal_code" => "test"
        ], [
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
    public function testGetAddressSuccess(){
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::where('street', 'test')->first();
        $this->get('/api/contacts/'.$address->contact_id.'/addresses/'.$address->id, [
            "Authorization" => "test"
        ])->assertStatus(200)
        ->assertJson([
             "data" => [
                "street" => "test",
                "city" => "test",
                "province" => "test",
                "country" => "test",
                "postal_code" => "test",
            ]
        ]);
    }
    public function testGetAddressNotFound(){
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::where('street', 'test')->first();
        $this->get('/api/contacts/'.$address->contact_id.'/addresses/'.$address->id + 1, [
            "Authorization" => "test"
        ])->assertStatus(404)
        ->assertJson([
             "errors" => [
                "message" => [
                    "Address not found"
                ]
            ]
        ]);
    }
    public function testUpdateAddressSuccess(){
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::where('street', 'test')->first();
        $this->put('/api/contacts/'.$address->contact_id.'/addresses/'.$address->id, [
            "street" => "ini berubah",
            "city" => "ini berubah",
            "province" => "ini berubah",
            "country" => "ini berubah",
            "postal_code" => "ini berubah",
        ], [
            "Authorization" => "test"
        ])->assertStatus(200)
        ->assertJson([
            "data" => [
                "street" => "ini berubah",
                "city" => "ini berubah",
                "province" => "ini berubah",
                "country" => "ini berubah",
                "postal_code" => "ini berubah",
            ]
        ]);
    }
    public function testUpdateAddressFailed(){
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::where('street', 'test')->first();
        $this->put('/api/contacts/'.$address->contact_id.'/addresses/'.$address->id, [
            "street" => "",
            "city" => "",
            "province" => "",
            "country" => "",
            "postal_code" => "",
        ], [
            "Authorization" => "test"
        ])->assertStatus(400)
        ->assertJson([
            "errors" => [
                "street" => ["The street field is required."],
                "city" => ["The city field is required."],
                "province" => ["The province field is required."],
                "country" => ["The country field is required."],
                "postal_code" => ["The postal code field is required."],
            ]
        ]);
    }
    public function testUpdateAddressNotFound(){
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::where('street', 'test')->first();
        $this->put('/api/contacts/'.$address->contact_id.'/addresses/'.$address->id + 1, [
            "street" => "ini berubah",
            "city" => "ini berubah",
            "province" => "ini berubah",
            "country" => "ini berubah",
            "postal_code" => "ini berubah",
        ], [
            "Authorization" => "test"
        ])->assertStatus(404)
        ->assertJson([
            "errors" => [
                "message" => [
                    "Address not found"
                ]
            ]
        ]);
    }
    public function testDeleteSuccess(){
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::where('street', 'test')->first();
        $this->delete('/api/contacts/'.$address->contact_id.'/addresses/'.$address->id, [],  [
            "Authorization" => "test"
        ])->assertStatus(200)
        ->assertJson([
            "data" => true
        ]);
    }
    public function testDeleteNotFound(){
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::where('street', 'test')->first();
        $this->delete('/api/contacts/'.$address->contact_id.'/addresses/'.$address->id + 1, [],  [
            "Authorization" => "test"
        ])->assertStatus(404)
        ->assertJson([
            "errors" => [
                "message" => [
                    "Address not found"
                ]
            ]
        ]);
    }
    public function testListSuccess(){
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::where('first_name', 'test')->first();
        $this->get('/api/contacts/'.$contact->id.'/addresses',  [
            "Authorization" => "test"
        ])->assertStatus(200)
        ->assertJson([
            "data" => [
                [
                    "street" => "test",
                    "city" => "test",
                    "province" => "test",
                    "country" => "test",
                    "postal_code" => "test",
                ]
            ]
        ]);
    }
    public function testListContactNotFound(){
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::where('first_name', 'test')->first();
        $this->get('/api/contacts/'.$contact->id + 1 .'/addresses',  [
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
}
