<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class LessonAccessTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function instructor_can_create_a_lesson_for_their_course(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);

        $course = Course::create([
            'title' => 'Test Course',
            'description' => 'Test Description',
            'instructor_id' => $instructor->id,
        ]);

        $response = $this->actingAs($instructor)->post(route('lessons.store', $course->id), [
            'title' => 'Lesson 1',
            'content' => 'Lesson content',
        ]);

        $this->assertDatabaseHas('lessons', [
            'course_id' => $course->id,
            'title' => 'Lesson 1',
        ]);

        $response->assertRedirect(route('courses.show', $course->id));
    }

    #[Test]
    public function student_not_enrolled_cannot_view_a_lesson(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);

        $course = Course::create([
            'title' => 'Test Course',
            'description' => 'Test Description',
            'instructor_id' => $instructor->id,
        ]);

        $lesson = Lesson::create([
            'course_id' => $course->id,
            'title' => 'Lesson 1',
            'content' => 'Secret content',
        ]);

        $response = $this->actingAs($student)->get(route('lessons.show', [$course->id, $lesson->id]));

        $response->assertStatus(403);
    }

    #[Test]
    public function enrolled_student_can_view_a_lesson(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);

        $course = Course::create([
            'title' => 'Test Course',
            'description' => 'Test Description',
            'instructor_id' => $instructor->id,
        ]);

        $lesson = Lesson::create([
            'course_id' => $course->id,
            'title' => 'Lesson 1',
            'content' => 'Allowed content',
        ]);

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
        ]);

        $response = $this->actingAs($student)->get(route('lessons.show', [$course->id, $lesson->id]));

        $response->assertStatus(200);
        $response->assertSee('Lesson 1');
    }
}
