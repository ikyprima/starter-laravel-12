<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AuthDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Roles
        $roles = [
            ["uuid" => "019c041a-0631-739e-af80-3a6b2881441a", "name" => "admin", "guard_name" => "web"],
            ["uuid" => "019c041a-063b-7314-9d23-f43868b3a61d", "name" => "skpd", "guard_name" => "web"],
            ["uuid" => "019c26a5-6780-7013-88ee-ebc9489b0092", "name" => "guest", "guard_name" => "web"],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(['name' => $roleData['name']], $roleData);
        }

        // 2. Seed Users
        $users = [
            [
                "id" => "060fb076-3235-4352-8581-f0f7397dd695",
                "name" => "Test User",
                "email" => "test@example.com",
                "password" => Hash::make("password"), // Default password for seeder
                "kode_sub_skpd" => null,
            ],
            [
                "id" => "1099db1d-9573-4e3f-8180-fb1dcdcef6b6",
                "name" => "adminbpkad",
                "email" => "adminbpkad@mail.com",
                "password" => Hash::make("password"),
                "kode_sub_skpd" => "5.02.0.00.0.00.01.0000",
            ],
            [
                "id" => "1319bfbc-6d0c-42d6-bd56-52913d3a8375",
                "name" => "Rizky Primadona",
                "email" => "ikyptes@gmail.com",
                "password" => Hash::make("password"),
                "kode_sub_skpd" => "5.02.0.00.0.00.01.0000",
            ],
            [
                "id" => "2d55e02e-982e-4e6c-a4cf-34d5859c9a7d",
                "name" => "rizky primadona",
                "email" => "primadona.rizky22@gmail.com",
                "password" => Hash::make("password"),
                "kode_sub_skpd" => null,
            ],
            [
                "id" => "622d90a6-eb57-407b-94bd-d186ae952ff8",
                "name" => "Administrator",
                "email" => "admin@example.com",
                "password" => Hash::make("password"),
                "kode_sub_skpd" => null,
            ],
            [
                "id" => "c27c36fb-e266-4d1d-9928-1e294c250aca",
                "name" => "admindinkes",
                "email" => "admindinkes@gmail.com",
                "password" => Hash::make("password"),
                "kode_sub_skpd" => "1.02.0.00.0.00.01.0000",
            ],
            [
                "id" => "f9f5f3e4-d107-4329-8f0f-9ab35ebf052e",
                "name" => "adminkominfo",
                "email" => "adminkominfo@mail.com",
                "password" => Hash::make("password"),
                "kode_sub_skpd" => "2.16.2.20.2.21.01.0000",
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['email' => $userData['email']], $userData);
        }

        // 3. Assign Roles to Users
        $userRoles = [
            ["role_id" => "019c041a-0631-739e-af80-3a6b2881441a", "model_id" => "060fb076-3235-4352-8581-f0f7397dd695"], // admin -> Test User
            ["role_id" => "019c041a-063b-7314-9d23-f43868b3a61d", "model_id" => "1099db1d-9573-4e3f-8180-fb1dcdcef6b6"], // skpd -> adminbpkad
            ["role_id" => "019c26a5-6780-7013-88ee-ebc9489b0092", "model_id" => "2d55e02e-982e-4e6c-a4cf-34d5859c9a7d"], // guest -> rizky primadona
            ["role_id" => "019c041a-063b-7314-9d23-f43868b3a61d", "model_id" => "c27c36fb-e266-4d1d-9928-1e294c250aca"], // skpd -> admindinkes
            ["role_id" => "019c041a-063b-7314-9d23-f43868b3a61d", "model_id" => "f9f5f3e4-d107-4329-8f0f-9ab35ebf052e"], // skpd -> adminkominfo
        ];

        foreach ($userRoles as $mapping) {
            DB::table('model_has_roles')->updateOrInsert([
                'role_id' => $mapping['role_id'],
                'model_id' => $mapping['model_id'],
                'model_type' => 'App\\Models\\User'
            ], $mapping);
        }
    }
}
