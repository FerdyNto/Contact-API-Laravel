<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    public function testRegisterSuccess()
    {
        $this->post('/api/users', [
            'username' => 'ferdynto',
            'password' => 'rahasia123',
            'name' => 'Ferdy Herdianto'
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    'username' => 'ferdynto',
                    'name' => 'Ferdy Herdianto'
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
                "errors" => [
                    'username' => [
                        'The username field is required.'
                    ],
                    'password' => [
                        'The password field is required.'
                    ],
                    'name' => [
                        'The name field is required.'
                    ],
                ]
            ]);
    }

    public function testRegisterUsernameAlreadyExists()
    {
        $this->testRegisterSuccess();
        $this->post('/api/users', [
            'username' => 'ferdynto',
            'password' => 'rahasia123',
            'name' => 'Ferdy Herdianto'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'username' => [
                        'username already registered'
                    ]
                ]
            ]);
    }
}
