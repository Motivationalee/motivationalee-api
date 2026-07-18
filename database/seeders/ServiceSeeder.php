<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            'Wellness Coach',
            'Success Coach',
            'Team Building',
            'Professional MC / Host',
            'Motivational Speaker',
            'Voice Over',
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['name' => $service],
                ['name' => $service]
            );
        }
    }
}
