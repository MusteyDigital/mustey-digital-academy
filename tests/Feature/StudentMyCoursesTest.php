<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class StudentMyCoursesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function student_can_see_enrolled_courses_on_my_courses_page(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);

        $course = Course::create([
            'title' => 'Test Course',
            'description' => 'Test Description',
            'instructor_id' => $instructor->id,
        ]);

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
        ]);

        $response = $this->actingAs($student)->get(route('enrollments.my-courses'));

        $response->assertStatus(200);
        $response->assertSee('Test Course');
    }
}
