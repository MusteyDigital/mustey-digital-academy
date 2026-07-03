<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $student = User::updateOrCreate(
            ['email' => 'demo-student@musteydigitalacademy.online'],
            ['name' => 'Demo Student', 'password' => Hash::make('demo1234'), 'role' => 'student']
        );

        foreach (Course::all() as $course) {
            Enrollment::firstOrCreate(
                ['user_id' => $student->id, 'course_id' => $course->id],
                ['enrolled_at' => now(), 'status' => 'active']
            );
        }

        User::updateOrCreate(
            ['email' => 'demo-instructor@musteydigitalacademy.online'],
            ['name' => 'Demo Instructor', 'password' => Hash::make('demo1234'), 'role' => 'instructor']
        );

        $this->command->info('Demo accounts created.');
    }
}