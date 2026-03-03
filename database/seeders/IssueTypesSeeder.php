<?php

namespace Database\Seeders;

use App\Models\IssueType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IssueTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $issueTypes = [
            ['name' => 'Guest Complaint'],
            ['name' => 'Maintenance Request'],
            ['name' => 'Equipment Failure'],
            ['name' => 'Cleanliness Issue'],
            ['name' => 'Service Quality'],
            ['name' => 'Staff Behavior'],
            ['name' => 'Food Quality'],
            ['name' => 'Billing Issue'],
            ['name' => 'Safety Concern'],
            ['name' => 'Security Issue'],
            ['name' => 'IT Problem'],
            ['name' => 'Facility Issue'],
            ['name' => 'Amenity Request'],
            ['name' => 'Lost & Found'],
            ['name' => 'Transportation'],
            ['name' => 'Reservation Issue'],
        ];

        foreach ($issueTypes as $issueType) {
            IssueType::firstOrCreate(['name' => $issueType['name']]);
        }
    }
}
