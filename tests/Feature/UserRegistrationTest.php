<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function student_can_register_and_is_redirected_to_student_dashboard(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Student',
            'email' => 'student@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'student@test.com',
            'role' => 'student',
        ]);

        $response->assertRedirect('/student/dashboard');
    }
}
