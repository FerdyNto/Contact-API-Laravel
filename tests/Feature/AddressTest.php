<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post(
            '/api/contacts/' . $contact->id . '/addresses',
            [
                'street' => 'Street Test',
                'city' => 'City Test',
                'province' => 'Province Test',
                'country' => 'Country Test',
                'postal_code' => '123123'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'street' => 'Street Test',
                    'city' => 'City Test',
                    'province' => 'Province Test',
                    'country' => 'Country Test',
                    'postal_code' => '123123'
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post(
            '/api/contacts/' . $contact->id . '/addresses',
            [
                'street' => 'Street Test',
                'city' => 'City Test',
                'province' => 'Province Test',
                'country' => '',
                'postal_code' => '123123'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'country' => [
                        'The country field is required.'
                    ]
                ]

            ]);
    }

    public function testCreateContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post(
            '/api/contacts/' . $contact->id + 1 . '/addresses',
            [
                'street' => 'Street Test',
                'city' => 'City Test',
                'province' => 'Province Test',
                'country' => 'Country Test',
                'postal_code' => '123123'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]

            ]);
    }
}