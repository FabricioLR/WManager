<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class ContactController extends Controller
{

    public function __construct(
        protected ContactService $contactService
    ){}

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'phone_number' => 'required|string',
        ]);

        try {
            $this->contactService->store($request->input('phone_number'));

            Log::info('Contact added successfully: ' . $request->input('phone_number'));

            return redirect()->back()->with('success', 'Contact added successfully!');
        } catch (Throwable $th) {
            Log::error('Error adding contact: ' . $th->getMessage());
            return redirect()->back()->withErrors(['phone_number' => $th->getMessage()]);
        }
    }
}