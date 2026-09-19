<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Settings\Models\FeatureSetting;
use App\Modules\Settings\Models\RoleFeatureSetting;
use Illuminate\Database\Seeder;

class RoleFeatureSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['admin', 'supervisor', 'finance', 'pic', 'employee', 'member', 'internship'];
        $features = FeatureSetting::pluck('key')->toArray();

        foreach ($roles as $role) {
            foreach ($features as $key) {
                RoleFeatureSetting::updateOrCreate(
                    ['role' => $role, 'feature_key' => $key],
                    ['is_enabled' => true],
                );
            }
        }
    }
}
