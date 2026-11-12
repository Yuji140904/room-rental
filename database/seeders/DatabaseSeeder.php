<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrNew(['phone' => '012345678']);
        $admin->name = 'Admin';
        $admin->password = 'Admin123';
        $admin->role = 'admin';
        $admin->save();

        foreach (['101', '102', '103', '201', '202', '203'] as $number) {
            Room::firstOrCreate(
                ['room_number' => $number],
                ['default_rent' => 400000, 'status' => 'vacant']
            );
        }
    }
}
