<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class CourseEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function student_can_enroll_in_a_course(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);

        $course = Course::create([
            'title' => 'PHP Basics',
            'description' => 'Learn PHP fundamentals',
            'instructor_id' => $instructor->id,
        ]);

        $response = $this->actingAs($student)->post(route('courses.enroll', $course->id));

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
        ]);

        $response->assertRedirect();
    }

    #[Test]
    public function student_cannot_enroll_twice_in_same_course(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);

        $course = Course::create([
            'title' => 'Laravel Advanced',
            'description' => 'Deep dive Laravel',
            'instructor_id' => $instructor->id,
        ]);

        $this->actingAs($student)->post(route('courses.enroll', $course->id));
        $this->actingAs($student)->post(route('courses.enroll', $course->id));

        $this->assertDatabaseCount('enrollments', 1);
    }
}
