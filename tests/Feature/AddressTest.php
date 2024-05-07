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

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->get('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id, [
            'Authorization' => 'test'
        ])->assertStatus(200)
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

    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->get('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id + 1, [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['not found']
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [
                'street' => 'Street Test Update',
                'city' => 'City Test Update',
                'province' => 'Province Test Update',
                'country' => 'Country Test Update',
                'postal_code' => '223223'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'street' => 'Street Test Update',
                    'city' => 'City Test Update',
                    'province' => 'Province Test Update',
                    'country' => 'Country Test Update',
                    'postal_code' => '223223'
                ]
            ]);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [
                'street' => 'Street Test Update',
                'city' => 'City Test Update',
                'province' => 'Province Test Update',
                'country' => '',
                'postal_code' => '223223'
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

    public function testUpdateNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id + 1,
            [
                'street' => 'Street Test Update',
                'city' => 'City Test Update',
                'province' => 'Province Test Update',
                'country' => 'Country Test Update',
                'postal_code' => '223223'
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

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->delete(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->delete(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id + 1,
            [],
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

    public function testListSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get(
            '/api/contacts/' . $contact->id . '/addresses',
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        "street" => "Street Test",
                        "city" => "City Test",
                        "province" => "Province Test",
                        "country" => "Country Test",
                        "postal_code" => "123123"
                    ]
                ]
            ]);
    }

    public function testListNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get(
            '/api/contacts/' . $contact->id + 1 . '/addresses',
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
