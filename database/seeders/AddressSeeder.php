<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contact = Contact::query()->limit(1)->first();
        Address::create([
            'contact_id' => $contact->id,
            'street' => 'Street Test',
            'city' => 'City Test',
            'province' => 'Province Test',
            'country' => 'Country Test',
            'postal_code' => '123123'
        ]);
    }
}
