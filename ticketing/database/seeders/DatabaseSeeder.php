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
        \App\Models\User::factory(10)->create();

        $users = [
                [
                'name' => 'Adrian Salinas',
                'email' => 'adriansalinas@slu.edu.ph',
                'password' => '123456',
                'department' => 'SAMCIS',
                ],
                [
                'name' => 'Matthew Serafica',
                'email' => 'Matthewserafica.slu.edu.ph',
                'password' => '123456',
                'department' => 'SEA',
                ],
                [
                'name' => 'Lovely Osngal',
                'email' => 'lovelyosngal@slu.edu.com',
                'password' => '123456',
                'department' => 'SEA',
                ],
                [
                'name' => 'Prince Taguiling',
                'email' => 'Princetaguiling@slu.edu.ph',
                'password' => '123456',
                'department' => 'SAMCIS',
                ],
                    
            ];

        foreach ($users as $user) {
            \App\Models\User::factory()->create($user);
        }

        \App\Models\Employee::factory()->create();
        $employees = [
            [
            'user_id' => '1',
            'department' => 'SAMCIS',
            ],
            [
            'user_id' => '2',
            'department' => 'SEA',
            ],
            [
            'user_id' => '3',
            'department' => 'SEA',
            ],
            [
            'user_id' => '4',
            'department' => 'SAMCIS',
            ],
                
        ];

        foreach ($employees as $employee) {
            \App\Models\Employee::factory()->create($employee);
        }

        \App\Models\Technician::factory()->create();
            $technicians = [
                [
                'user_id' => '1',
                ],
                [
                'user_id' => '2',
                ],
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

        \App\Models\Ticket::factory()->create();
            
            $tickets = [
                [
                'employee' => '1',
                'technician' => '',
                'issue' => 'Network Issue',
                'description' => 'test',
                'status' => 'New',
                ],
                [
                'employee' => '2',
                'technician' => '',
                'issue' => 'Hardware Issue',
                'description' => 'broken monitor',
                'status' => 'New',
                ],
                [
                'employee' => '3',
                'technician' => '',
                'issue' => 'Hardware Issue',
                'description' => 'broken keyboard',
                'status' => 'New',
                ],
                [
                'employee' => '4',
                'technician' => '',
                'issue' => 'Network Issue',
                'description' => 'No internet connection',
                'status' => 'New',
                ],
                    
            ];
        foreach ($tickets as $ticket) {
            \App\Models\Ticket::factory()->create($ticket);
        }
        
    }
}
