<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Exception;

class DashboardController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.dashboard', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'is_admin' => 'nullable|boolean',
        ]);

        try{
            User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_admin' => $request->has('is_admin'),
            ]);

            return redirect()->route('dashboard')->with('success', 'User created successfully.');
        } catch (Exception $e){
            return redirect()->route('dashboard')->withError(['error' => 'Cannot create user: ' + $e->getMessage()]);
        }
    }
}
