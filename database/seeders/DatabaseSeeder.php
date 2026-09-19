<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Groups\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed data awal: akun admin + grup default.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@taskarts.app'],
            [
                'name' => 'Administrator',
                'password' => 'secret123',
                'role' => 'admin',
            ],
        );

        $groups = [
            ['name' => 'TIM IT', 'description' => 'Tim infrastruktur teknologi informasi, jaringan, dan keamanan sistem.'],
            ['name' => 'TIM BUSINESS', 'description' => 'Tim pengembangan bisnis, pemasaran, dan hubungan klien.'],
            ['name' => 'TIM DEVELOPER', 'description' => 'Tim pengembang software, engineer, dan product development.'],
        ];

        foreach ($groups as $groupData) {
            $group = Group::updateOrCreate(
                ['name' => $groupData['name']],
                $groupData,
            );

            // Admin otomatis masuk semua grup sebagai admin
            $admin->groups()->syncWithoutDetaching([
                $group->id => ['role' => 'admin'],
            ]);
        }

        $this->call(FeatureSettingsSeeder::class);
    }
}
