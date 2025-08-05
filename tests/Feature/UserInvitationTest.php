<?php

namespace Tests\Feature;

use App\Events\UserInvited;
use App\Mail\UserInvitationMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class UserInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created_without_password(): void
    {
        $userData = [
            'name' => 'Jan Kowalski',
            'email' => 'jan@example.com',
            'phone' => '+48123456789',
            'amount' => 200,
            'is_active' => true,
        ];

        $user = User::create($userData);

        $this->assertDatabaseHas('users', [
            'email' => 'jan@example.com',
            'name' => 'Jan Kowalski',
        ]);

        $this->assertNull($user->password);
    }

    public function test_user_invitation_event_is_dispatched(): void
    {
        Event::fake();

        $user = User::create([
            'name' => 'Jan Kowalski',
            'email' => 'jan@example.com',
            'amount' => 200,
        ]);

        UserInvited::dispatch($user);

        Event::assertDispatched(UserInvited::class, function ($event) use ($user) {
            return $event->user->id === $user->id;
        });
    }

    public function test_invitation_email_is_sent(): void
    {
        Mail::fake();

        $user = User::create([
            'name' => 'Jan Kowalski',
            'email' => 'jan@example.com',
            'amount' => 200,
        ]);

        $token = Password::createToken($user);
        Mail::to($user->email)->send(new UserInvitationMail($user, $token));

        Mail::assertSent(UserInvitationMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_set_password_form_is_accessible(): void
    {
        $user = User::create([
            'name' => 'Jan Kowalski',
            'email' => 'jan@example.com',
            'amount' => 200,
        ]);

        $token = Password::createToken($user);
        
        // Zapisz token w bazie danych (zahashowany)
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => \Illuminate\Support\Facades\Hash::make($token),
                'created_at' => now(),
            ]
        );

        $url = route('set-password', ['token' => $token, 'email' => $user->email]);

        $response = $this->get($url);

        $response->assertStatus(200);
        $response->assertSee('Ustaw hasło');
        $response->assertSee($user->name);
    }

    public function test_user_can_set_password(): void
    {
        $user = User::create([
            'name' => 'Jan Kowalski',
            'email' => 'jan@example.com',
            'amount' => 200,
        ]);

        $token = Password::createToken($user);
        
        // Zapisz token w bazie danych (zahashowany)
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => \Illuminate\Support\Facades\Hash::make($token),
                'created_at' => now(),
            ]
        );

        $response = $this->post(route('set-password.store'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();

        $user->refresh();
        $this->assertNotNull($user->password);
        $this->assertTrue(Auth::check());
    }

    public function test_user_without_password_cannot_access_panel(): void
    {
        $user = User::create([
            'name' => 'Jan Kowalski',
            'email' => 'jan@example.com',
            'amount' => 200,
        ]);

        $this->actingAs($user);

        $response = $this->get('/panel');

        $response->assertStatus(403);
    }

    public function test_user_with_incomplete_profile_is_redirected(): void
    {
        $user = User::create([
            'name' => 'Jan Kowalski',
            'email' => 'jan@example.com',
            'amount' => 200,
            'password' => bcrypt('password123'),
        ]);

        $this->actingAs($user);

        $response = $this->get('/panel');

        $response->assertStatus(403);
    }
}
