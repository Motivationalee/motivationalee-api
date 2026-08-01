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
            'Homepage',
            'Keynote Speaker',
            'Professional MC & Host',
            'Event Experience Director'
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['name' => $service],
                ['name' => $service]
            );
        }
    }
}
