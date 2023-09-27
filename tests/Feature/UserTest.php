<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->post('/api/users', [
            'username' => 'davidxyolo',
            'password' => 'test',
            'name' => 'David'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'username' => 'davidxyolo',
                    'name' => 'David'
                ]
            ]);
    }

    public function testRegisterFailed()
    {
        $this->post('/api/users', [
            'username' => '',
            'password' => '',
            'name' => ''
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
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

    public function testUsernameAlreadyExists()
    {
        $this->testRegisterSuccess();
        $this->post('/api/users', [
            'username' => 'davidxyolo',
            'password' => 'test',
            'name' => 'David'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'username' => [
                        "The username has already been taken."
                    ]
                ]
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'davidxyolo',
            'password' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'davidxyolo',
                    'name' => 'David'
                ]
            ]);

        $user = User::where('username', 'davidxyolo')->first();
        self::assertNotNull($user->token);
    }

    public function testLoginFailedUsernameNotFound()
    {
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'test'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    "message" => [
                        "username or password wrong"
                    ]
                ]
            ]);
    }

    public function testLoginFailedPasswordWrong()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'davidxyolo',
            'password' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    "message" => [
                        "username or password wrong"
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'davidxyolo',
                    'name' => 'David'
                ]
            ]);
    }

    public function testGetUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current')
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);

    }

    public function testGetInvalidToken()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            'Authorization' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'davidxyolo')->first();

        $this->patch(
            '/api/users/current',
            [
                'password' => 'baru'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'davidxyolo',
                    'name' => 'David'
                ]
            ]);

        $newUser = User::where('username', 'davidxyolo')->first();
        self::assertNotEquals($oldUser->password, $newUser->password);
    }

    public function testUpdateNameSuccess()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'davidxyolo')->first();

        $this->patch(
            '/api/users/current',
            [
                'name' => 'asep'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'davidxyolo',
                    'name' => 'asep'
                ]
            ]);

        $newUser = User::where('username', 'davidxyolo')->first();
        self::assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->patch(
            '/api/users/current',
            [
                'name' => 'EkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEko'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        "The name must not be greater than 100 characters."
                    ]
                ]
            ]);
    }

    public function testLogoutSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);

        $user = User::where('username', 'davidxyolo')->first();
        self::assertNull($user->token);

    }

    public function testLogoutFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }


}