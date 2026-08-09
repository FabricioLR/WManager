<?php

namespace App\Http\Controllers;

use App\Models\Contact;

class ChatController extends Controller
{
    public function index()
    {
        $contacts = Contact::with(['messages' => function ($query) {
            $query->latest('timestamp')->limit(1);
        }])
        ->orderByDesc('last_message_from_contact_at')
        ->get();

        return view('index', compact('contacts'));
    }

    public function show(Contact $contact)
    {
        $contacts = Contact::with(['messages' => function ($query) {
            $query->latest('timestamp')->limit(1);
        }])
        ->orderByDesc('last_message_from_contact_at')
        ->get();

        $messages = $contact->messages()->get();

        return view('index', compact('contacts', 'contact', 'messages'));
    }
}