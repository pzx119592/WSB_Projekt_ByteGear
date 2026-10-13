<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderConfirmation;
use App\Services\OrderMail;
use Database\Seeders\ShopSeeder;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class ShopExtensionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->seed(ShopSeeder::class);
    }

    private function user(string $role = 'customer'): User
    {
        return User::where('role', $role)->firstOrFail();
    }

    private function placeOrder(): Order
    {
        $products = Product::orderBy('id')->take(2)->get();
        $token = (string) Str::uuid();
        $this->actingAs($this->user())->withSession(['cart' => [$products[0]->id => 2, $products[1]->id => 1], 'checkout_token' => $token]);
        $this->post('/zamowienie', ['name' => 'Jan Testowy', 'address' => 'Testowa 12', 'postcode' => '00-001', 'city' => 'Warszawa', 'checkout_token' => $token])->assertRedirect();

        return Order::latest('id')->firstOrFail();
    }

    private function productData(): array
    {
        return ['name' => 'Produkt testowy', 'sku' => 'PHOTO-1', 'category_id' => 1, 'description' => 'Opis produktu testowego', 'price' => '100.00', 'stock' => 3, 'active' => 1];
    }

    public function test_profile_edits_name_but_cannot_change_role_or_other_accounts(): void
    {
        $user = $this->user();
        $this->actingAs($user)->get('/profil')->assertOk();
        $this->patch('/profil', ['name' => 'Nowe Imie', 'email' => $user->email, 'role' => 'admin', 'id' => $this->user('admin')->id])->assertRedirect();
        $this->assertSame('Nowe Imie', $user->fresh()->name);
        $this->assertSame('customer', $user->fresh()->role);
        $this->assertSame('Administrator', $this->user('admin')->name);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_email_change_requires_password_and_resets_verification(): void
    {
        $user = $this->user();
        $data = ['name' => $user->name, 'email' => 'new@example.test'];
        $this->actingAs($user)->patch('/profil', $data)->assertSessionHasErrors('current_password');
        $this->patch('/profil', $data + ['current_password' => 'wrong'])->assertSessionHasErrors('current_password');
        $this->assertSame('customer@example.test', $user->fresh()->email);
        $this->patch('/profil', $data + ['current_password' => 'DemoSklep123!'])->assertRedirect('/potwierdz-email');
        $this->assertSame('new@example.test', $user->fresh()->email);
        $this->assertNull($user->fresh()->email_verified_at);
        Notification::assertSentTo($user, VerifyEmail::class);
        $this->get('/konto')->assertRedirect('/potwierdz-email');
        $this->get('/profil')->assertOk();
    }

    public function test_profile_rejects_duplicate_email_and_weak_or_incorrect_password(): void
    {
        $user = $this->user();
        $this->actingAs($user)->patch('/profil', ['name' => $user->name, 'email' => 'admin@example.test', 'current_password' => 'DemoSklep123!'])->assertSessionHasErrors('email');
        $this->put('/profil/haslo', ['current_password' => 'wrong', 'password' => 'NoweHaslo123!', 'password_confirmation' => 'NoweHaslo123!'])->assertSessionHasErrors('current_password');
        $this->put('/profil/haslo', ['current_password' => 'DemoSklep123!', 'password' => 'weak', 'password_confirmation' => 'weak'])->assertSessionHasErrors('password');
        $this->assertTrue(Hash::check('DemoSklep123!', $user->fresh()->password));
    }

    public function test_password_change_revokes_other_sessions_and_keeps_current_login(): void
    {
        $user = $this->user();
        config(['session.driver' => 'database']);
        DB::table('sessions')->insert(['id' => 'other-session', 'user_id' => $user->id, 'payload' => '', 'last_activity' => time()]);
        $this->actingAs($user)->put('/profil/haslo', ['current_password' => 'DemoSklep123!', 'password' => 'NoweHaslo123!', 'password_confirmation' => 'NoweHaslo123!'])->assertRedirect();
        $this->assertTrue(Hash::check('NoweHaslo123!', $user->fresh()->password));
        $this->assertDatabaseMissing('sessions', ['id' => 'other-session']);
        $this->get('/profil')->assertOk();
        $this->assertAuthenticatedAs($user);
    }

    public function test_only_staff_can_upload_and_uploaded_images_are_reencoded_and_replaceable(): void
    {
        Storage::fake('local');
        $data = $this->productData();
        $this->actingAs($this->user())->post('/panel/products', $data + ['image' => UploadedFile::fake()->image('photo.png', 320, 320)])->assertForbidden();
        $this->actingAs($this->user('moderator'))->post('/panel/products', $data + ['image' => UploadedFile::fake()->image('photo.png', 320, 320)])->assertRedirect('/panel/products');
        $product = Product::where('sku', 'PHOTO-1')->firstOrFail();
        $firstPath = $product->image_path;
        $this->assertStringEndsWith('.jpg', $firstPath);
        Storage::disk('local')->assertExists($firstPath);
        $this->get('/produkty/'.$product->id.'/zdjecie')->assertOk()->assertHeader('Content-Type', 'image/jpeg');
        $this->put('/panel/products/'.$product->id, $data + ['image' => UploadedFile::fake()->image('new.jpg', 400, 400)])->assertRedirect();
        Storage::disk('local')->assertMissing($firstPath);
        $newPath = $product->fresh()->image_path;
        Storage::disk('local')->assertExists($newPath);
        $this->put('/panel/products/'.$product->id, $data + ['remove_image' => 1])->assertRedirect();
        Storage::disk('local')->assertMissing($newPath);
        $this->assertNull($product->fresh()->image_path);
    }

    public function test_upload_rejects_svg_disguised_files_dimensions_and_oversize(): void
    {
        Storage::fake('local');
        $this->actingAs($this->user('admin'));
        foreach ([UploadedFile::fake()->createWithContent('evil.jpg', '<script>alert(1)</script>'), UploadedFile::fake()->createWithContent('evil.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>'), UploadedFile::fake()->image('tiny.png', 20, 20), UploadedFile::fake()->image('large.png', 320, 320)->size(5000)] as $file) {
            $this->post('/panel/products', $this->productData() + ['image' => $file])->assertSessionHasErrors('image');
        }
        $this->assertDatabaseMissing('products', ['sku' => 'PHOTO-1']);
        $this->assertEmpty(Storage::disk('local')->allFiles());
    }

    public function test_cancel_restocks_multiple_items_once_and_preserves_history_even_when_archived(): void
    {
        $before = Product::orderBy('id')->take(2)->get()->pluck('stock', 'id');
        $order = $this->placeOrder();
        Product::first()->update(['active' => false]);
        $this->patch('/zamowienia/'.$order->id.'/anuluj', ['confirm_cancel' => 1])->assertRedirect();
        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->cancelled_at);
        $this->assertSame($before->all(), Product::orderBy('id')->take(2)->get()->pluck('stock', 'id')->all());
        $this->patch('/zamowienia/'.$order->id.'/anuluj', ['confirm_cancel' => 1])->assertRedirect();
        $this->assertSame($before->all(), Product::orderBy('id')->take(2)->get()->pluck('stock', 'id')->all());
        $this->assertDatabaseCount('order_items', 2);
        $this->get('/zamowienia/'.$order->id)->assertOk()->assertSee('Anulowane');
    }

    public function test_cannot_cancel_someone_elses_or_processing_order_without_confirmation(): void
    {
        $order = $this->placeOrder();
        $this->patch('/zamowienia/'.$order->id.'/anuluj')->assertSessionHasErrors('confirm_cancel');
        $other = User::factory()->create(['role' => 'customer']);
        $this->actingAs($other)->patch('/zamowienia/'.$order->id.'/anuluj', ['confirm_cancel' => 1])->assertForbidden();
        $order->update(['status' => 'processing']);
        $stock = Product::first()->stock;
        $this->actingAs($this->user())->patch('/zamowienia/'.$order->id.'/anuluj', ['confirm_cancel' => 1])->assertSessionHasErrors('order');
        $this->assertSame($stock, Product::first()->stock);
        $this->assertSame('processing', $order->fresh()->status);
    }

    public function test_confirmation_uses_order_snapshots_and_only_sends_once(): void
    {
        $order = $this->placeOrder();
        Notification::assertSentOnDemand(OrderConfirmation::class, fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === $order->email);
        $this->assertNotNull($order->confirmation_sent_at);
        Product::first()->update(['name' => 'Zmieniony produkt', 'price_grosze' => 1]);
        $html = view('mail.order-confirmation', ['order' => $order->load('items')])->render();
        $this->assertStringContainsString('BG K75 Mechanical', $html);
        $this->assertStringNotContainsString('Zmieniony produkt', $html);
        app(OrderMail::class)->send($order);
        Notification::assertSentOnDemandTimes(OrderConfirmation::class, 1);
    }

    public function test_smtp_failure_does_not_undo_purchase_and_can_be_retried(): void
    {
        Notification::shouldReceive('send')->once()->andThrow(new TransportException('SMTP offline'));
        $order = $this->placeOrder();
        $this->assertNull($order->confirmation_sent_at);
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 2);
        Notification::fake();
        $this->post('/zamowienia/'.$order->id.'/potwierdzenie')->assertRedirect();
        Notification::assertSentOnDemand(OrderConfirmation::class);
        $this->assertNotNull($order->fresh()->confirmation_sent_at);
    }
}
