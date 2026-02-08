<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Profile;
use App\Models\Service;
use App\Models\ServiceVariant;
use App\Models\User;
use App\Models\CalendarSetting;
use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class JankaSchillerSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'jana@schiller.sk'],
            [
                'name' => 'Jana Schiller',
                'password' => Hash::make('password'),
                'role' => 'owner',
            ]
        );

        $profile = Profile::updateOrCreate(
            ['slug' => 'janka-schiller'],
            [
                'owner_id' => $user->id,
                'name' => ['sk' => 'Jana Schiller Salon Escape'],
                'timezone' => 'Europe/Bratislava',
                'status' => 'active',
            ]
        );

        CalendarSetting::updateOrCreate(
            ['profile_id' => $profile->id],
            [
                'slot_interval_minutes' => 30,
                'max_advance_days' => 90,
            ]
        );

        // Working hours: Mon-Fri 09:00 - 17:00, Sat 09:00 - 13:00
        for ($i = 1; $i <= 5; $i++) {
            Schedule::updateOrCreate(
                ['profile_id' => $profile->id, 'day_of_week' => $i],
                [
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                ]
            );
        }
        Schedule::updateOrCreate(
            ['profile_id' => $profile->id, 'day_of_week' => 6],
            [
                'start_time' => '09:00',
                'end_time' => '13:00',
            ]
        );

        $employee = Employee::updateOrCreate(
            ['profile_id' => $profile->id, 'name' => 'Jana Schiller'],
            ['is_active' => true]
        );

        // Services
        $s1 = Service::updateOrCreate(
            ['profile_id' => $profile->id, 'name->sk' => 'Korekcia pier'],
            ['base_duration_minutes' => 120, 'base_price' => 70, 'is_active' => true]
        );
        if ($s1->variants()->count() === 0) {
            $s1->variants()->createMany([
                ['name' => 'korekcia do 6.mesiacov', 'duration_minutes' => 120, 'price' => 70],
                ['name' => 'Refresh 6-12 mesiacov', 'duration_minutes' => 120, 'price' => 100],
                ['name' => 'Refresh 12-24 mesiacov', 'duration_minutes' => 150, 'price' => 130],
            ]);
        }
        $s1->employees()->sync([$employee->id]);

        $s2 = Service::updateOrCreate(
            ['profile_id' => $profile->id, 'name->sk' => 'Korekcia liniek'],
            ['base_duration_minutes' => 120, 'base_price' => 70, 'is_active' => true]
        );
        if ($s2->variants()->count() === 0) {
            $s2->variants()->createMany([
                ['name' => 'korekcia do 6.mesiacov', 'duration_minutes' => 120, 'price' => 70],
                ['name' => 'Refresh od 6 do 12 mesiacov', 'duration_minutes' => 120, 'price' => 100],
                ['name' => 'Refresh od 12 do 24 mesiacov', 'duration_minutes' => 150, 'price' => 130],
            ]);
        }
        $s2->employees()->sync([$employee->id]);

        $s3 = Service::updateOrCreate(
            ['profile_id' => $profile->id, 'name->sk' => 'Make up'],
            [
                'base_duration_minutes' => 45,
                'base_price' => 40,
                'is_active' => true,
                'is_special' => true,
                'slot_interval_minutes' => 15
            ]
        );
        if ($s3->variants()->count() === 0) {
            $s3->variants()->createMany([
                ['name' => 'Večerný make up', 'duration_minutes' => 45, 'price' => 40],
                ['name' => 'Svadobný make up (+skúška)', 'duration_minutes' => 150, 'price' => 110],
            ]);
        }
        $s3->employees()->sync([$employee->id]);

        // Availability rules for Make up: Fri 14:00-17:00, Sat 09:00-13:00
        $s3->availabilityRules()->delete();
        $s3->availabilityRules()->createMany([
            ['day_of_week' => 5, 'start_time' => '14:00', 'end_time' => '17:00'],
            ['day_of_week' => 6, 'start_time' => '09:00', 'end_time' => '13:00'],
        ]);

        // Restricted Service (demonstration of new feature)
        // This service is ONLY available between 13:00 and 15:00
        // and uses a 10 minute slot interval instead of the default 30.
        $s4 = Service::updateOrCreate(
            ['profile_id' => $profile->id, 'name->sk' => 'Špeciálna poobedná služba'],
            [
                'base_duration_minutes' => 30,
                'base_price' => 50,
                'is_active' => true,
                'is_special' => true,
                'slot_interval_minutes' => 10,
            ]
        );
        $s4->employees()->sync([$employee->id]);

        $s4->availabilityRules()->delete();
        $s4->availabilityRules()->create([
            'start_time' => '13:00',
            'end_time' => '15:00',
        ]);
    }
}
