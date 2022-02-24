<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\RegisteredMailNotification;
use App\Providers\RouteServiceProvider;
use Database\Seeders\UserCreatekeySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register()
    {
        Notification::fake();

        $this->seed(UserCreatekeySeeder::class);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'create_authentication' => 'password123',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        Notification::assertSentTo($user, RegisteredMailNotification::class);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_new_users_can_not_register()
    {
        $this->seed(UserCreatekeySeeder::class);

        $response = $this
            ->from(route('user.register'))
            ->post('/register', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        $response->assertRedirect(route('user.register'));
    }
}
