<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
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

    // register
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

    // Login
    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'test'
                ]
            ]);

        // cek user di database
        $user = User::where('username', 'test')->first();
        // cek token tidak boleh kosong
        self::assertNotNull($user->token);
    }

    public function testLoginFailedUserNotFound()
    {
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'test'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'username or password wrong'
                    ]
                ]
            ]);
    }

    public function testLoginFailedPasswordWrong()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'username or password wrong'
                    ]
                ]
            ]);
    }

    // get user
    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'test'
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

    // update
    public function testUpdateNameSuccess()
    {
        $this->seed([UserSeeder::class]);
        $olduser = User::where('username', 'test')->first();

        $this->patch(
            '/api/users/current',
            [
                'name' => 'Dika'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'Dika'
                ]
            ]);

        $newuser = User::where('username', 'test')->first();
        self::assertNotEquals($olduser->name, $newuser->name);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed([UserSeeder::class]);
        $olduser = User::where('username', 'test')->first();

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
                    'username' => 'test',
                    'name' => 'test'
                ]
            ]);

        $newuser = User::where('username', 'test')->first();
        self::assertNotEquals($olduser->password, $newuser->password);
    }

    public function testUpdateNameFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->patch(
            '/api/users/current',
            [
                'name' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Quod optio veritatis, adipisci soluta quis labore voluptate officiis doloremque placeat cupiditate, eveniet expedita? Fugiat temporibus debitis repellendus quibusdam eos! Voluptates sed ex inventore ipsam expedita culpa excepturi eum error quod consectetur, aspernatur hic ducimus tempora quasi sit officiis, temporibus corrupti. Quam, officiis reiciendis? Dolorem, cupiditate magnam suscipit distinctio doloremque exercitationem, ea laudantium odit itaque accusamus eaque dignissimos in praesentium ab eligendi optio beatae perferendis soluta cum nobis sed veniam aspernatur sunt? Aperiam tempora molestiae nostrum neque, tempore saepe tenetur esse! Optio soluta ad fugit neque voluptatibus nam qui explicabo adipisci aut magni magnam laboriosam tempore laborum perferendis ratione non quasi repudiandae sed, quis mollitia nostrum necessitatibus unde! Ipsam dolore ullam repudiandae voluptatem incidunt commodi tempore, hic temporibus reprehenderit aliquid minus ea vitae corrupti fuga corporis reiciendis similique, adipisci itaque facere, quae placeat recusandae consequuntur. Ea est modi alias magni ipsam voluptate?'
            ],

            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name field must not be greater than 100 characters.'
                    ]
                ]
            ]);
    }

    public function testUpdateUnAuthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->patch(
            '/api/users/current',
            [
                'name' => 'test'
            ],

            [
                'Authorization' => 'salah'
            ]
        )->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }
}
