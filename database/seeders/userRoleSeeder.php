<?php

namespace Database\Seeders;

use App\Models\_user_roles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class userRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        _user_roles::insert([
            [
                'ROLE_NAME' => 'RM_ADMINISTRATOR',
            ],
            [
                'ROLE_NAME' => 'RM_GUARDIAN',
            ],
            [
                'ROLE_NAME' => 'RM_TEACHER',
            ],
            ]);
    }
}
