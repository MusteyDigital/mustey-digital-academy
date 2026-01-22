<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function enrolled_student_can_mark_attendance(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);

        $course = Course::create([
            'title' => 'Data Analysis',
            'description' => 'Intro',
            'instructor_id' => $instructor->id,
        ]);

        $lesson = Lesson::create([
            'course_id' => $course->id,
            'title' => 'Lesson 1',
            'content' => 'Hello',
        ]);

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
        ]);

        $response = $this->actingAs($student)->post(route('attendance.store', [$course->id, $lesson->id]));

        $response->assertStatus(302);

        $this->assertDatabaseHas('attendances', [
            'lesson_id' => $lesson->id,
            'user_id' => $student->id,
            'status' => 'present',
        ]);
    }

    #[Test]
    public function student_cannot_mark_attendance_twice(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);

        $course = Course::create([
            'title' => 'Data Analysis',
            'description' => 'Intro',
            'instructor_id' => $instructor->id,
        ]);

        $lesson = Lesson::create([
            'course_id' => $course->id,
            'title' => 'Lesson 1',
            'content' => 'Hello',
        ]);

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
        ]);

        $this->actingAs($student)->post(route('attendance.store', [$course->id, $lesson->id]));
        $this->actingAs($student)->post(route('attendance.store', [$course->id, $lesson->id]));

        $this->assertDatabaseCount('attendances', 1);
    }

    #[Test]
    public function not_enrolled_student_cannot_mark_attendance(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);

        $course = Course::create([
            'title' => 'Data Analysis',
            'description' => 'Intro',
            'instructor_id' => $instructor->id,
        ]);

        $lesson = Lesson::create([
            'course_id' => $course->id,
            'title' => 'Lesson 1',
            'content' => 'Hello',
        ]);

        $response = $this->actingAs($student)->post(route('attendance.store', [$course->id, $lesson->id]));
        $response->assertStatus(403);
    }

    #[Test]
    public function instructor_can_view_attendance_list(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);

        $course = Course::create([
            'title' => 'Data Analysis',
            'description' => 'Intro',
            'instructor_id' => $instructor->id,
        ]);

        $lesson = Lesson::create([
            'course_id' => $course->id,
            'title' => 'Lesson 1',
            'content' => 'Hello',
        ]);

        Attendance::create([
            'lesson_id' => $lesson->id,
            'user_id' => $student->id,
            'status' => 'present',
            'marked_at' => now(),
        ]);

        $response = $this->actingAs($instructor)->get(route('attendance.index', [$course->id, $lesson->id]));
        $response->assertStatus(200);
        $response->assertSee('Attendance');
    }
}
