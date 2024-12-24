<?php

namespace Database\Seeders;

use App\Models\_settings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        _settings::insert([
            [
                'SET_ID' => 'APP_NAME',
                'SET_VALUE' => 'Talentaku',
                'SET_VALUE_TEXT' => '-',
                'SET_INFO' => 'nama aplikasi',
                'SET_DISPLAY_FORM' => 'Y',
                'SET_VALUE_DISPLAY_FORM' => 'N',
            ],
            [
                'SET_ID' => 'LOGIN_EXPIRED_DAYS',
                'SET_VALUE' => '7',
                'SET_VALUE_TEXT' => '-',
                'SET_INFO' => 'nama expired token',
                'SET_DISPLAY_FORM' => 'Y',
                'SET_VALUE_DISPLAY_FORM' => 'N',
            ],
            [
                'SET_ID' => 'APP_LOGO',
                'SET_VALUE' => '-',
                'SET_VALUE_TEXT' => '-',
                'SET_INFO' => 'logo aplikasi',
                'SET_DISPLAY_FORM' => 'N',
                'SET_VALUE_DISPLAY_FORM' => 'Y',
            ],
        ]);
    }
}
