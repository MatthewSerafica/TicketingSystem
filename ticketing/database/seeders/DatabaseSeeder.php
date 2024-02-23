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
                'technician' => '1',
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

        $service_reports = [
            [
                'date_started' => '2022-02-22',
                'time_started' => '08:00:00',
                'ticket_number' => 1,
                'technician_name' => 'John Doe',
                'requesting_office' => 'IT Department',
                'equipment_no' => 'E123',
                'issue' => 'Network connectivity problem',
                'action' => 'Checked cables and fixed the issue',
                'recommendation' => 'Install additional network equipment',
                'date_done' => '2022-02-22',
                'time_done' => '10:00:00',
                'technicians_id' => 3, 
            ],
        ];
        foreach ($service_reports as $service_reports) {
            \App\Models\ServiceReport::factory()->create($service_reports);
        }

        $departments =[
            [
                'department' => 'LES'
            ],
            [
                'department' => 'JHS'
            ],
            [
                'department' => 'SHS'
            ],
            [
                'department' => 'SAMCIS'
            ],
            [
                'department' => 'SAS'
            ],
            [
                'department' => 'SEA'
            ],
            [
                'department' => 'SOL'
            ],
            [
                'department' => 'SOM'
            ],
            [
                'department' => 'SONAHBS'
            ],
            [
                'department' => 'STELA'
            ],
        ];
        foreach ($departments as $departments) {
            \App\Models\Department::factory()->create($departments);
        }

        $offices =[
            [
                'office' => 'Registrars Office'
            ],
            [
                'office' => 'Deans office'
            ],
            [
                'office' => 'library'
            ],
            [
                'office' => 'Research and Innovation Office'
            ],
            [
                'office' => 'Finance Office'
            ],
            [
                'office' => 'Printing Operations Office'
            ],
            [
                'office' => 'Technology Management and Development Department'
            ],
            [
                'office' => 'Center for Culture and the Arts'
            ],
            [
                'office' => 'Dental Clinic'
            ],
            [
                'office' => 'Guidance Center'
            ],
            [
                'office' => 'Human Resource Department'
            ],
            [
                'office' => 'Students Residence Hall'
            ],
            [
                'office' => 'Medical Clinic'
            ],
            [
                'office' => 'Office of Legal Affairs'
            ],
            [
                'office' => 'Office of Students Affairs'
            ],
            [
                'office' => 'SLU Sacred Heart Medical Center'
            ],
        ];
        foreach ($offices as $offices) {
            \App\Models\Office::factory()->create($offices);
        }

    }
}
