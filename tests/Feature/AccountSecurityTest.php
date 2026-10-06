<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\ShopSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AccountSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    private function customer(): User
    {
        return User::factory()->create(['role' => 'customer', 'email_verified_at' => null]);
    }

    private function signed(User $user, $expires = null): string
    {
        return URL::temporarySignedRoute('verification.verify', $expires ?? now()->addHour(), ['id' => $user->id, 'hash' => sha1($user->email)]);
    }

    public function test_registration_sends_activation_and_unverified_account_cannot_checkout(): void
    {
        $this->post('/rejestracja', ['name' => 'Nowy Klient', 'email' => 'new@example.test', 'password' => 'DobreHaslo123!', 'password_confirmation' => 'DobreHaslo123!'])->assertRedirect('/logowanie');
        $user = User::where('email', 'new@example.test')->firstOrFail();
        Notification::assertSentTo($user, VerifyEmail::class);
        $this->assertNull($user->email_verified_at);
        $this->actingAs($user)->get('/zamowienie')->assertRedirect('/potwierdz-email');
        $this->get('/konto')->assertRedirect('/potwierdz-email');
        $this->get('/potwierdz-email')->assertOk()->assertSee($user->email);
    }

    public function test_signed_link_activates_only_its_authenticated_owner(): void
    {
        $user = $this->customer();
        $link = $this->signed($user);
        $this->get($link)->assertRedirect('/logowanie');
        $this->actingAs($this->customer())->get($link)->assertForbidden();
        $this->assertNull($user->fresh()->email_verified_at);
        $this->actingAs($user)->get($link)->assertRedirect('/konto');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->get('/konto')->assertOk();
        $this->get('/potwierdz-email')->assertRedirect('/konto');
    }

    public function test_expired_and_tampered_activation_links_are_rejected(): void
    {
        $user = $this->customer();
        $this->actingAs($user)->get($this->signed($user, now()->subMinute()))->assertForbidden();
        $this->get($this->signed($user).'tampered')->assertForbidden();
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_resend_is_limited_and_verified_staff_gate_is_enforced(): void
    {
        $user = $this->customer();
        $user->update(['role' => 'admin']);
        $this->actingAs($user)->get('/panel/users')->assertRedirect('/potwierdz-email');
        foreach (range(1, 3) as $ignored) {
            $this->post('/wyslij-aktywacje')->assertRedirect();
        }
        Notification::assertSentToTimes($user, VerifyEmail::class, 3);
        $this->post('/wyslij-aktywacje')->assertStatus(429);
    }

    public function test_admin_created_account_and_changed_email_require_activation(): void
    {
        $this->seed(ShopSeeder::class);
        $admin = User::where('role', 'admin')->firstOrFail();
        $customer = User::where('role', 'customer')->firstOrFail();
        $this->actingAs($admin)->post('/panel/users', ['name' => 'Nowy Klient', 'email' => 'created@example.test', 'role' => 'customer', 'password' => 'DobreHaslo123!', 'password_confirmation' => 'DobreHaslo123!'])->assertRedirect('/panel/users');
        $new = User::where('email', 'created@example.test')->firstOrFail();
        Notification::assertSentTo($new, VerifyEmail::class);
        $this->assertNull($new->email_verified_at);
        $oldLink = $this->signed($customer);
        $this->put('/panel/users/'.$customer->id, ['name' => $customer->name, 'email' => 'changed@example.test', 'role' => 'customer'])->assertRedirect('/panel/users');
        $this->assertNull($customer->fresh()->email_verified_at);
        Notification::assertSentTo($customer, VerifyEmail::class);
        $this->actingAs($customer->fresh())->get($oldLink)->assertForbidden();
    }

    public function test_reset_request_hides_whether_an_account_exists_and_token_is_hashed(): void
    {
        $user = $this->customer();
        $this->from('/zapomniane-haslo')->post('/zapomniane-haslo', ['email' => $user->email])->assertRedirect('/zapomniane-haslo');
        $message = session('status');
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $stored = DB::table('password_reset_tokens')->where('email', $user->email)->value('token');
            $this->assertNotSame($notification->token, $stored);
            $this->assertTrue(Hash::check($notification->token, $stored));

            return true;
        });
        $this->post('/zapomniane-haslo', ['email' => 'absent@example.test'])->assertRedirect();
        $this->assertSame($message, session('status'));
        $this->assertDatabaseCount('password_reset_tokens', 1);
    }

    public function test_reset_changes_hash_revokes_database_sessions_and_token_cannot_be_reused(): void
    {
        $user = $this->customer();
        $token = Password::createToken($user);
        config(['session.driver' => 'database']);
        DB::table('sessions')->insert(['id' => 'old-login', 'user_id' => $user->id, 'payload' => '', 'last_activity' => time()]);
        $data = ['token' => $token, 'email' => $user->email, 'password' => 'NoweHaslo123!', 'password_confirmation' => 'NoweHaslo123!'];
        $this->post('/nowe-haslo', $data)->assertRedirect('/logowanie');
        $this->assertTrue(Hash::check('NoweHaslo123!', $user->fresh()->password));
        $this->assertNotEmpty($user->fresh()->remember_token);
        $this->assertDatabaseMissing('sessions', ['id' => 'old-login']);
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
        $this->post('/nowe-haslo', $data)->assertSessionHasErrors('email');
        $this->post('/logowanie', ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->post('/logowanie', ['email' => $user->email, 'password' => 'NoweHaslo123!'])->assertRedirect('/konto');
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_reset_rejects_expired_wrong_token_and_weak_password(): void
    {
        $user = $this->customer();
        $token = Password::createToken($user);
        $data = ['token' => $token, 'email' => $user->email, 'password' => 'NoweHaslo123!', 'password_confirmation' => 'NoweHaslo123!'];
        $this->post('/nowe-haslo', array_replace($data, ['token' => 'wrong']))->assertSessionHasErrors('email');
        $this->post('/nowe-haslo', array_replace($data, ['password' => 'weak', 'password_confirmation' => 'weak']))->assertSessionHasErrors('password');
        $this->travel(61)->minutes();
        $this->post('/nowe-haslo', $data)->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_mail_uses_configured_brand_and_local_reset_url(): void
    {
        config(['app.name' => 'Nowa Nazwa']);
        $user = $this->customer();
        $activation = (new VerifyEmail)->toMail($user);
        $reset = (new ResetPassword('test-token'))->toMail($user);
        $this->assertStringContainsString('Nowa Nazwa', $activation->subject);
        $this->assertStringContainsString('Nowa Nazwa', $reset->subject);
        $this->assertStringContainsString('/nowe-haslo/test-token', $reset->actionUrl);
        $this->get('/logowanie')->assertSee('Nie pamiętam hasła');
        $this->get('/zapomniane-haslo')->assertOk();
        $this->get('/nowe-haslo/test-token?email='.urlencode($user->email))->assertOk()->assertSee($user->email);
    }
}
