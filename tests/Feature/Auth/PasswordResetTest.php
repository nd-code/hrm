<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/'.$notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }

    public function test_employee_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/employee/forgot-password');

        $response->assertStatus(200);
    }

    public function test_employee_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $employee = \App\Models\Employee::create([
            'name' => 'Test Employee',
            'email' => 'employee@example.com',
            'password' => bcrypt('password'),
            'status' => 1,
        ]);

        $response = $this->post('/forgot-password', ['email' => $employee->email]);

        $response->assertSessionHas('status');
        Notification::assertSentTo($employee, ResetPassword::class);
    }

    public function test_inactive_employee_cannot_request_reset_password_link(): void
    {
        Notification::fake();

        $employee = \App\Models\Employee::create([
            'name' => 'Inactive Employee',
            'email' => 'inactive@example.com',
            'password' => bcrypt('password'),
            'status' => 0,
        ]);

        $response = $this->post('/forgot-password', ['email' => $employee->email]);

        $response->assertSessionHasErrors(['email' => 'Your account is inactive. Please contact admin.']);
        Notification::assertNothingSent();
    }

    public function test_employee_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $employee = \App\Models\Employee::create([
            'name' => 'Test Employee',
            'email' => 'employee@example.com',
            'password' => bcrypt('old-password'),
            'status' => 1,
        ]);

        $this->post('/forgot-password', ['email' => $employee->email]);

        Notification::assertSentTo($employee, ResetPassword::class, function ($notification) use ($employee) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $employee->email,
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            $employee->refresh();
            $this->assertTrue(\Illuminate\Support\Facades\Hash::check('new-password', $employee->password));

            return true;
        });
    }
}
