<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class CourseCreationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function instructor_can_create_a_course(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
        ]);

        $response = $this->actingAs($instructor)->post('/courses', [
            'title' => 'Laravel Basics',
            'description' => 'Introduction to Laravel framework',
        ]);

        $this->assertDatabaseHas('courses', [
            'title' => 'Laravel Basics',
            'instructor_id' => $instructor->id,
        ]);

        $response->assertRedirect(route('courses.index'));
    }

    #[Test]
    public function student_cannot_create_a_course(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
        ]);

        $response = $this->actingAs($student)->post('/courses', [
            'title' => 'Unauthorized Course',
            'description' => 'Students should not create courses',
        ]);

        $response->assertStatus(403);
    }
}
