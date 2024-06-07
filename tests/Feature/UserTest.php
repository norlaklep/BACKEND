<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\User;
use Database\Seeders\UserSeeder;

use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertNotNull;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testRegisterSuccess()
    {
        $response = $this->postJson('/api/users', [
            'name' => 'fajar',
            'email' => 'fajar@gmail.com',
            'password' => 'fajar123',
            'password_confirmation' => 'fajar123',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => 'fajar',
                    'email' => 'fajar@gmail.com',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'fajar',
            'email' => 'fajar@gmail.com',
        ]);
    }

    public function testRegisterFail()
    {
        $response = $this->postJson('/api/users', [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function testRegisterNameAlreadyExist()
    {
        User::create([
            'name' => 'fajar',
            'email' => 'fajar2@gmail.com',
            'password' => bcrypt('fajar123'),
        ]);

        $response = $this->postJson('/api/users', [
            'name' => 'fajar',
            'email' => 'fajar3@gmail.com',
            'password' => 'fajar123',
            'password_confirmation' => 'fajar123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function testRegisterEmailAlreadyExist()
    {
        User::create([
            'name' => 'fajar',
            'email' => 'fajar2@gmail.com',
            'password' => bcrypt('fajar123'),
        ]);

        $response = $this->postJson('/api/users', [
            'name' => 'fajar2',
            'email' => 'fajar2@gmail.com',
            'password' => 'fajar123',
            'password_confirmation' => 'fajar123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function testLoginSuccess()
    {
        $user = User::create([
            'name' => 'fajar',
            'email' => 'test@gmail.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/users/login', [
            'email' => 'test@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'token',
                ]
            ]);

        $this->assertNotNull($user->fresh()->token);
    }

    public function testLoginFailureWrongEmail()
    {
        $user = User::create([
            'name' => 'fajar',
            'email' => 'test@gmail.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/users/login', [
            'email' => 'wrong@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Invalid credentials',
            ]);
    }

    public function testLoginFailureWrongPassword()
    {
        $user = User::create([
            'name' => 'fajar',
            'email' => 'test@gmail.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/users/login', [
            'email' => 'test@gmail.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Invalid credentials',
            ]);
    }

    public function testGetSuccess()
    {
        $user = User::create([
            'name' => 'fajar',
            'email' => 'test@gmail.com',
            'password' => bcrypt('password123'),
            'token' => (string) Str::uuid(),
        ]);
    
        // Perform the request with the correct token
        $response = $this->getJson('/api/users/current', [
            'Authorization' => 'Bearer ' . $user->token,
        ]);
    
        // Assert the response status and structure
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'fajar',
                    'email' => 'test@gmail.com',
                ]
            ]);
    }
    
    public function testGetUnauthorized()
    {
        $user = User::create([
            'name' => 'fajar',
            'email' => 'test@gmail.com',
            'password' => bcrypt('password123'),
            'token' => (string) Str::uuid(),
        ]);
        $response = $this->getJson('/api/users/current');
    
        $response->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => ['unauthorized'],
                ]
            ]);
    }
    
    public function testGetInvalid()
    {
        $user = User::create([
            'name' => 'fajar',
            'email' => 'test@gmail.com',
            'password' => bcrypt('password123'),
            'token' => (string) Str::uuid(),
        ]);
        $response = $this->getJson('/api/users/current', [
            'Authorization' => 'invalid-token',
        ]);
    
        $response->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => ['unauthorized'],
                ]
            ]);
    }
    

    public function testUpdateNameSuccess()
    {
        User::create([
            'name'=> 'test',
            'email'=> 'test@gmail.com',
            'password'=> Hash::make('test123'),
            'token' => (string) Str::uuid(),
        ]);
        $olduser = User::where('name','test')->first();
        $this->patchJson('/api/users/current',
        [
            'name'=> 'fajar1',
        ],
        [
            'Authorization' => 'test'
        ]
        )->assertStatus(200)
            ->assertJson([
                'data'=> [
                    'name'=> 'fajar1',
                ]
            ]);
        $newuser = User::where('name','fajar1')->first();
        self::assertNotEquals($olduser->name, $newuser->name);
    }

    public function testupdatePasswordSucess()
    {
        User::create([
            'name'=> 'test',
            'email'=> 'test@gmail.com',
            'password'=> Hash::make('test123'),
            'token' => (string) Str::uuid(),
        ]);
        $olduser = User::where('password','test123')->first();
        $this->patchJson('/api/users/current',
        [
            'password'=> 'fajar123',
        ],
        [
            'Authorization' => 'test'
        ]
        )->assertStatus(200)
            ->assertJson([
                'data'=> [
                    'name'=> 'test',
                ]
            ]);
        $newuser = User::where('password','fajar123')->first();
        self::assertNotEquals($olduser->password, $newuser->password);
    }

    public function testupdatefailed()
    {
        User::create([
            'name'=> 'test',
            'email'=> 'test@gmail.com',
            'password'=> bcrypt     ('test123'),
            'token' => (string) Str::uuid(),
        ]);
        $olduser = User::where('password','test123')->first();
        $this->patchJson('/api/users/current',
        [
            'password'=> 'fajar',
        ],
        [
            'Authorization' => 'test'
        ]
        )->assertStatus(401)
            ->assertJson([
                'error'=> [
                    'password'=> [
                        "Password minimal 6."
                    ]
                ]
            ]);
    }

    public function testLogoutSuccess()
    {
        User::create([
            'name'=> 'test',
            'email'=> 'test@gmail.com',
            'password'=> bcrypt     ('test123'),
            'token' => (string) Str::uuid(),
        ]);

    }

    public function testLogoutFailure()
    {

    }


}
