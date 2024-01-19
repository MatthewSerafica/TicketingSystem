<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $users = [
                [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'test'
                ],
                [
                'name' => 'Test Technician',
                'email' => 'technician@example.com',
                'password' => 'test'
                ],
            ];

        foreach ($users as $user) {
            \App\Models\User::factory()->create($user);
        }

        \App\Models\Employee::factory()->create([
            'user_id' => '1',
            'department' => 'test department',
            'made_ticket' => '0', 
        ]);

        \App\Models\Technician::factory()->create([
            'user_id' => '2',
        ]);

        \App\Models\Ticket::factory()->create([
            'employee_id' => '1',
        ]);
    }
}
