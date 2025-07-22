<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::create([
            'id' => Role::ADMIN_ROLE_ID,
            'name' => 'admin',
            'title' => 'مدیر'
        ]);

        Role::create([
            'id' => Role::SERVICE_PROVIDER_ROLE_ID,
            'name' => 'service_provider',
            'title' => 'میزبان'
        ]);

        Role::create([
            'id' => Role::SERVICE_CONSUMER_ROLE_ID,
            'name' => 'service_consumer',
            'title' => 'میهمان'
        ]);

        $adminUser = User::create([
            'first_name' => 'admin',
            'last_name' => 'admin',
            'email' => 'admin@jagir.ir',
            'phone' => '09999999999',
            'national_code' => '1111111111',
            'email_verified_at' => now(),
            'identity_verified_at' => now(),
            'phone_verified_at' => now(),
            'password' => Hash::make('password')
        ]);

        $adminUser->roles()->attach($adminRole);
    }
}
