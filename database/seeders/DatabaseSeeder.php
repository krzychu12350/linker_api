<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventPoll;
use App\Models\Report;
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
//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);
//        $this->call([
//           RolesAndPermissionsSeeder::class,
//            UserDetailSeeder::class,
//        ]);
//
////        User::factory(20)->withRole('user')->create();
//        User::factory(20)->withRole('moderator')->create();
//        User::factory(1)->withRole('user')->create([
//            'first_name' => 'Test',
//            'last_name' => 'Test',
//            'email' => 'TEST@TEST.pl',
//            'password' => Hash::make('securepassword123')
//        ]);
//        User::factory(1)->withRole('user')->create([
//            'first_name' => 'Test2',
//            'last_name' => 'Test2',
//            'email' => 'TEST2@TEST.pl',
//            'password' => Hash::make('securepassword123')
//        ]);
//
//        Report::factory(20)->create();
        $this->call([
            EventPollSeeder::class,
        ]);
    }
}
