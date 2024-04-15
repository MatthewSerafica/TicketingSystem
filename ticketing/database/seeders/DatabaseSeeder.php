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
                    
            ];

        foreach ($users as $user) {
            \App\Models\User::factory()->create($user);
        }

        /* $employees = [
            [
            'user_id' => '2',
            'department' => 'SEA',
            'office' => 'Dental Clinic',
            ],
            [
            'user_id' => '5',
            'department' => 'SAMCIS',
            'office' => 'Guidance Center'
            ],
                
        ];

        foreach ($employees as $employee) {
            \App\Models\Employee::factory()->create($employee);
        } */

            /* $technicians = [
                [
                'user_id' => '3'
                ],
                [
                'user_id' => '4',
                ],
                    
            ];

        foreach ($technicians as $technician) {
            \App\Models\Technician::factory()->create($technician);
        } */

            
            /* $tickets = [
                
                    
            ];
        foreach ($tickets as $ticket) {
            \App\Models\Ticket::factory()->create($ticket);
        } */

        /* $service_reports = [
            [
                
            ],
        ];
        foreach ($service_reports as $service_reports) {
            \App\Models\ServiceReport::factory()->create($service_reports);
        } */

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

        $services = [
            [
            'service' => 'Network Troubleshoot',
            ],
            [
            'service' => 'Hardware Repair',
            ],
            [
            'service' => 'Software Troubleshoot',
            ],
            [
            'service' => 'Network Troubleshoot',
            ],
                
        ];
        foreach ($services as $services) {
            \App\Models\Service::factory()->create($services);
        }

        $problem = [
            [
            'problem' => 'No Internet Connection',
            ],
            [
            'problem' => 'Computer is Slow',
            ],
            [
            'problem' => 'Monitor Black Screen',
            ],
            [
            'problem' => 'Bent HDMI cord',
            ],
                
        ];
        foreach ($problem as $problem) {
            \App\Models\Problem::factory()->create($problem);
        }

    }
}
