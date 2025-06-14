<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::create([
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
    }
}
