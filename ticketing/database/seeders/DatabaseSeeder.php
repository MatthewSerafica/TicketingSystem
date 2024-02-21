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
        $users = [
                [
                'name' => 'Jon Doe',
                'email' => 'jondoe@slu.edu.ph',
                'password' => '123456',
                'user_type' => 'admin',
                ],
                [
                'name' => 'Adrian Salinas',
                'email' => 'adriansalinas@slu.edu.ph',
                'password' => '123456',
                'user_type' => 'employee',
                ],
                [
                'name' => 'Matthew Serafica',
                'email' => 'matthewserafica@slu.edu.ph',
                'password' => '123456',
                'user_type' => 'technician',
                ],
                [
                'name' => 'Lovely Osngal',
                'email' => 'lovelyosngal@slu.edu.com',
                'password' => '123456',
                'user_type' => 'technician',
                ],
                [
                'name' => 'Prince Taguiling',
                'email' => 'princetaguiling@slu.edu.ph',
                'password' => '123456',
                'user_type' => 'employee',
                ],
                    
            ];

        foreach ($users as $user) {
            \App\Models\User::factory()->create($user);
        }

        $employees = [
            [
            'user_id' => '2',
            'department' => 'SEA',
            ],
            [
            'user_id' => '5',
            'department' => 'SAMCIS',
            ],
                
        ];

        foreach ($employees as $employee) {
            \App\Models\Employee::factory()->create($employee);
        }

            $technicians = [
                [
                'user_id' => '3'
                ],
                [
                'user_id' => '4',
                ],
                    
            ];

        foreach ($technicians as $technician) {
            \App\Models\Technician::factory()->create($technician);
        }

            
            $tickets = [
                [
                'employee' => '1',
                'issue' => 'Hardware Issue',
                'service' => 'Hardware Repair',
                'description' => 'broken monitor',
                'status' => 'Pending',
                ],
                [
                'employee' => '2',
                'issue' => 'Network Issue',
                'description' => 'No internet connection',
                'status' => 'New',
                'service' => 'Software Troubleshoot',
                ],
                    
            ];
        foreach ($tickets as $ticket) {
            \App\Models\Ticket::factory()->create($ticket);
        }
        
    }
}
