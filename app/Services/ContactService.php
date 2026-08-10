<?php

namespace App\Services;

use App\Models\Contact;

class ContactService
{
    public function store(string $phoneNumber): Contact
    {
        $contact = Contact::firstOrCreate(
            ['wa_id' => $phoneNumber],
            [
                'name' => $phoneNumber,
                'phone_number' => $phoneNumber
            ]
        );

        return $contact;
    }
}