<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess(){
        $this->post('/api/users',[
            'name' => 'test',
            'username' => 'test',
            'password' => 'test'
        ])->assertStatus(201)
        ->assertJson([
            "data" => [
                'name' => 'test',
                'username' => 'test',
            ]
        ]);
    }
    public function testRegisterFailed(){
        $this->post('/api/users',[
            'name' => '',
            'username' => '',
            'password' => ''
        ])->assertStatus(400)
        ->assertJson([
            "errors" => [
                'username' => [
                    "The username field is required."
                ],
                'password' => [
                    "The password field is required."
                ],
                'name' => [
                    "The name field is required."
                ]
            ]
        ]);
    }
    public function testRegisterUsernameAlreadyExists(){
        $this->testRegisterSuccess();
        $this->post('/api/users',[
            'name' => 'test',
            'username' => 'test',
            'password' => 'test'
        ])->assertStatus(400)
        ->assertJson([
            "errors" => [
                'username' => [
                    "Username already registered"
                ]
            ]
        ]);
    }
    public function testLoginSuccess(){
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login',[
            'name' => 'test',
            'username' => 'test',
            'password' => 'test'
        ])->assertStatus(200)
        ->assertJson([
            "data" => [
                "username" => "test",
                "name" => "test",
            ]
        ]);
        $user = User::where('username', 'test')->first();
        $this->assertNotNull($user->token);
    }
    public function testLoginFailed(){
        $this->post('/api/users/login',[
            'name' => 'test',
            'username' => 'test',
            'password' => 'test'
        ])->assertStatus(401)
        ->assertJson([
            "errors" => [
                "message" => [
                    "Username or password is wrong"
                ]
            ]
        ]);
    }
    public function testLoginFailedPasswordWrong(){
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login',[
            'name' => 'test',
            'username' => 'test',
            'password' => 'wrong'
        ])->assertStatus(401)
        ->assertJson([
            "errors" => [
                "message" => [
                    "Username or password is wrong"
                ]
            ]
        ]);
    }
    public function testGetSuccess(){
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current', [
            "Authorization" => "test"
        ])->assertStatus(200)
        ->assertJson([
            "data" => [
                "username" => "test",
                "name" => "test",
            ]
        ]);
    }
    public function testGetUnauthorized(){
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current')
        ->assertStatus(401)
        ->assertJson([
            "errors" => [
                "message" => [
                    "Unauthorized"
                ]
            ]
        ]);
    }
    public function testGetInvalidToken(){
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current', [
            "Authorization" => "salah"
        ])->assertStatus(401)
        ->assertJson([
            "errors" => [
                "message" => [
                    "Unauthorized"
                ]
            ]
        ]);
    }
    public function testUpdatePasswordSuccess(){
        $this->seed([UserSeeder::class]);
        $oldPassword = User::where('username', 'test')->first()->password;
        $this->patch('/api/users/current', [
            'password' => 'baru',
        ], [
            "Authorization" => "test"
        ])->assertStatus(200)
        ->assertJson([
            "data" => [
                "username" => "test",
                "name" => "test",
            ]
        ]);
        $newPassword = User::where('username', 'test')->first()->password;
        $this->assertNotEquals($oldPassword, $newPassword);
    }
    public function testUpdateNameSuccess(){
        $this->seed([UserSeeder::class]);
        $oldName = User::where('username', 'test')->first()->name;
        $this->patch('/api/users/current', [
            'name' => 'earl',
        ], [
            "Authorization" => "test"
        ])->assertStatus(200)
        ->assertJson([
            "data" => [
                "username" => "test",
                "name" => "earl",
            ]
        ]);
        $newName = User::where('username', 'test')->first()->name;
        $this->assertNotEquals($oldName, $newName);
    }
    public function testUpdateFailed(){
        $this->seed([UserSeeder::class]);
        $oldName = User::where('username', 'test')->first()->name;
        $this->patch('/api/users/current', [
            'name' => 'earlphantomhiveearlphantomhiveearlphantomhiveearlphantomhiveearlphantomhiveearlphantomhiveearlphantomhiveearlphantomhiveearlphantomhiveearlphantomhiveearlphantomhive',
        ], [
            "Authorization" => "test"
        ])->assertStatus(400)
        ->assertJson([
            "errors" => [
                "name" => [
                    "The name field must not be greater than 100 characters."
                ]
            ]
        ]);
        $newName = User::where('username', 'test')->first()->name;
        $this->assertEquals($oldName, $newName);
    }
    public function testLogoutSuccess(){
        $this->seed([UserSeeder::class]);
        $this->delete(uri: '/api/users/logout', headers: [
            "Authorization" => "test"
        ])->assertStatus(200)
        ->assertJson([
            "data" => true
        ]);
        $user = User::where('username', 'test')->first();
        $this->assertNull($user->token);
    }
    public function testLogoutFailed(){
        $this->seed([UserSeeder::class]);
        $this->delete(uri: '/api/users/logout', headers: [
            "Authorization" => "salah"
        ])->assertStatus(401)
        ->assertJson([
            "errors" => [
                "message" => [
                    "Unauthorized"
                ]
            ]
        ]);
    }
}
