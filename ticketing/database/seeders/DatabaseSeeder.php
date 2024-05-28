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
                'user_type' => 'super',
                ],
            ];

        foreach ($users as $user) {
            \App\Models\User::factory()->create($user);
        }


        $departments =[
            [
                'department' => 'BEDS'
            ],
            [
                'department' => 'SEA'
            ],
            [
                'department' => 'NATSCI'
            ],
            [
                'department' => 'Finance'
            ],
            [
                'department' => 'Admin'
            ],
            [
                'department' => 'SOL'
            ],
            [
                'department' => 'Junior High'
            ],
            [
                'department' => 'OSA'
            ],
            [
                'department' => 'STELA'
            ],
            [
                'department' => 'SAMCIS'
            ],
            [
                'department' => 'Senior High'
            ],
            [
                'department' => 'SAS'
            ],
        ];
        foreach ($departments as $department) {
            \App\Models\Department::factory()->create($department);
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
        foreach ($offices as $office) {
            \App\Models\Office::factory()->create($office);
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
        foreach ($services as $service) {
            \App\Models\Service::factory()->create($service);
        }

        $problems = [
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
        foreach ($problems as $problem) {
            \App\Models\Problem::factory()->create($problem);
        }

    }
}
