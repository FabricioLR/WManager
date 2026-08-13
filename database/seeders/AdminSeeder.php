<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $email = config('admin.email', env('ADMIN_EMAIL', 'admin@admin.com'));
        $username = config('admin.username', env('ADMIN_USERNAME', 'admin'));
        $password = config('admin.password', env('ADMIN_PASSWORD', 'admin'));
        $api_token = config('admin.api_token', env('ADMIN_API_TOKEN', '123456'));

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $username,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'api_token' => $api_token,
                'is_admin' => true,
            ]
        );

        Log::info('Default administrator account successfully synchronized via environment variables.', ['email' => $email]);
    }
}
