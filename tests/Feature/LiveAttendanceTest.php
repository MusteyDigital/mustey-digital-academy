<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class LiveAttendanceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function enrolled_student_can_mark_live_attendance(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);

        $course = Course::create([
            'title' => 'Data Analysis',
            'description' => 'Intro',
            'meeting_url' => 'https://example.com/meet',
            'starts_at' => now(),
            'instructor_id' => $instructor->id,
        ]);

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
        ]);

        $this->actingAs($student)->post("/courses/{$course->id}/attendance/live");

        $this->assertDatabaseHas('attendances', [
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => 'present',
        ]);
    }

    #[Test]
    public function student_cannot_mark_live_attendance_twice(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);

        $course = Course::create([
            'title' => 'Data Analysis',
            'description' => 'Intro',
            'meeting_url' => 'https://example.com/meet',
            'starts_at' => now(),
            'instructor_id' => $instructor->id,
        ]);

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
        ]);

        $response = $this->actingAs($student)->post("/courses/{$course->id}/attendance/live");
        $response->assertStatus(302);

        $this->actingAs($student)->post("/courses/{$course->id}/attendance/live");

        $this->assertDatabaseCount('attendances', 1);
    }

    #[Test]
    public function instructor_can_view_live_attendance_list(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);

        $course = Course::create([
            'title' => 'Data Analysis',
            'description' => 'Intro',
            'meeting_url' => 'https://example.com/meet',
            'starts_at' => now(),
            'instructor_id' => $instructor->id,
        ]);

        $response = $this->actingAs($instructor)->get("/courses/{$course->id}/attendance/live");

        $response->assertStatus(200);
        $response->assertSee('Live Session Attendance');
    }
}
