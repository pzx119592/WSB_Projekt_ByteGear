<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\ShopSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ShopSeeder::class);
        Http::preventStrayRequests();
    }

    private function user(string $role = 'customer'): User
    {
        return User::where('role', $role)->firstOrFail();
    }

    private function delivery(string $token): array
    {
        return ['name' => 'Jan Testowy', 'address' => 'Testowa 12', 'postcode' => '00-001', 'city' => 'Warszawa', 'checkout_token' => $token];
    }

    public function test_catalog_filter_sort_and_brand_configuration(): void
    {
        config(['app.name' => 'Nowa Nazwa']);
        $this->get('/')->assertOk()->assertSee('Nowa Nazwa')->assertDontSee('ByteGear');
        $this->get('/?q=Keyline')->assertOk()->assertSee('Keyline K75')->assertDontSee('Track M1');
        $this->get('/?min=900&max=1100')->assertOk()->assertSee('View 27 QHD')->assertDontSee('Keyline');
        $this->get('/?sort=price_asc')->assertOk()->assertSeeInOrder(['Desk Mat Soft', 'Link Cable Pro']);
        $this->get('/?sort=price_grosze;DROP%20TABLE%20users')->assertSessionHasErrors('sort');
        $this->assertDatabaseCount('users', 3);
    }

    public function test_registration_hashes_password_and_cannot_assign_admin_role(): void
    {
        $this->post('/rejestracja', ['name' => 'Nowy Klient', 'email' => 'new@example.test', 'password' => 'DobreHaslo123!', 'password_confirmation' => 'DobreHaslo123!', 'role' => 'admin'])
            ->assertRedirect('/logowanie');
        $user = User::where('email', 'new@example.test')->firstOrFail();
        $this->assertSame('customer', $user->role);
        $this->assertTrue(Hash::check('DobreHaslo123!', $user->password));
        $this->post('/rejestracja', ['name' => 'Duplikat', 'email' => 'new@example.test', 'password' => 'DobreHaslo123!', 'password_confirmation' => 'DobreHaslo123!'])->assertSessionHasErrors('email');
    }

    public function test_each_role_has_its_dashboard_and_backend_permissions(): void
    {
        foreach (['customer' => null, 'moderator' => '/panel/products', 'admin' => '/panel/users'] as $role => $redirect) {
            $this->actingAs($this->user($role));
            $response = $this->get('/konto');
            $redirect ? $response->assertRedirect($redirect) : $response->assertOk()->assertSee('PANEL KLIENTA');
            $this->get('/panel/users')->assertStatus($role === 'admin' ? 200 : 403);
            $this->get('/panel/products')->assertStatus($role === 'customer' ? 403 : 200);
        }
    }

    public function test_login_and_logout_work_and_wrong_password_is_rejected(): void
    {
        $this->post('/logowanie', ['email' => 'customer@example.test', 'password' => 'blad'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->post('/logowanie', ['email' => 'customer@example.test', 'password' => 'DemoSklep123!'])->assertRedirect('/konto');
        $this->assertAuthenticatedAs($this->user());
        $this->post('/wyloguj')->assertRedirect('/');
        $this->assertGuest();
        $this->get('/panel/users')->assertRedirect('/logowanie');
    }

    public function test_cart_add_update_remove_and_stock_limit(): void
    {
        $product = Product::first();
        $this->post('/koszyk/'.$product->id, ['quantity' => 2])->assertSessionHas('cart.'.$product->id, 2);
        $this->patch('/koszyk/'.$product->id, ['quantity' => 3])->assertSessionHas('cart.'.$product->id, 3);
        $this->patch('/koszyk/'.$product->id, ['quantity' => 99])->assertSessionHasErrors('quantity');
        $this->get('/koszyk')->assertOk()->assertSee($product->name);
        $this->delete('/koszyk/'.$product->id)->assertSessionMissing('cart.'.$product->id);
    }

    public function test_checkout_uses_database_price_snapshots_and_decrements_stock_once(): void
    {
        $product = Product::first();
        $token = (string) Str::uuid();
        $originalStock = $product->stock;
        $this->actingAs($this->user())->withSession(['cart' => [$product->id => 2], 'checkout_token' => $token]);
        $this->post('/zamowienie', $this->delivery($token) + ['price_grosze' => 1, 'total_grosze' => 1])->assertRedirect();
        $order = Order::firstOrFail();
        $this->assertSame($product->price_grosze * 2, $order->total_grosze);
        $this->assertSame($originalStock - 2, $product->fresh()->stock);
        $this->assertDatabaseCount('order_items', 1);
        $this->assertSame($product->name, $order->items->first()->name);
        $this->post('/zamowienie', $this->delivery($token))->assertSessionHasErrors('order');
        $this->assertDatabaseCount('orders', 1);
        $product->update(['name' => 'Zmieniony produkt', 'price_grosze' => 1]);
        $this->get('/zamowienia/'.$order->id)->assertOk()->assertSee('Keyline K75')->assertDontSee('Zmieniony produkt');
    }

    public function test_insufficient_stock_rolls_back_entire_order(): void
    {
        $products = Product::take(2)->get();
        $token = (string) Str::uuid();
        $products[1]->update(['stock' => 0]);
        $this->actingAs($this->user())->withSession(['cart' => [$products[0]->id => 1, $products[1]->id => 1], 'checkout_token' => $token]);
        $this->post('/zamowienie', $this->delivery($token))->assertSessionHasErrors('order');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertSame($products[0]->stock, $products[0]->fresh()->stock);
    }

    public function test_checkout_after_changing_quantity_in_real_form(): void
    {
        $product = Product::first();
        $token = (string) Str::uuid();
        $this->actingAs($this->user())->post('/koszyk/'.$product->id, ['quantity' => '1']);
        $this->patch('/koszyk/'.$product->id, ['quantity' => '2'])->assertRedirect();
        $this->withSession(['checkout_token' => $token])->post('/zamowienie', $this->delivery($token))->assertRedirect();
        $this->assertDatabaseCount('orders', 1);
        $this->assertSame(2, (int) Order::first()->items->first()->quantity);
    }

    public function test_invalid_postcode_and_guests_cannot_place_orders(): void
    {
        $token = (string) Str::uuid();
        $this->post('/zamowienie', $this->delivery($token))->assertRedirect('/logowanie');
        $this->actingAs($this->user())->post('/zamowienie', array_replace($this->delivery($token), ['postcode' => '12345']))->assertSessionHasErrors('postcode');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_other_customer_cannot_read_order(): void
    {
        $order = $this->createOrder();
        $other = User::factory()->create(['role' => 'customer']);
        $this->actingAs($other)->get('/zamowienia/'.$order->id)->assertForbidden();
        $this->get('/zamowienia')->assertDontSee('Jan Testowy');
        $this->actingAs($this->user('moderator'))->get('/zamowienia/'.$order->id)->assertOk();
    }

    public function test_admin_user_full_crud_and_preservation_of_order_history(): void
    {
        $order = $this->createOrder();
        $customer = $this->user();
        $this->actingAs($this->user('admin'));
        $data = ['name' => 'Nowy Moderator', 'email' => 'mod2@example.test', 'role' => 'moderator', 'password' => 'HasloTest123!', 'password_confirmation' => 'HasloTest123!'];
        $this->post('/panel/users', $data)->assertRedirect('/panel/users');
        $new = User::where('email', $data['email'])->firstOrFail();
        $this->assertTrue(Hash::check($data['password'], $new->password));
        $this->get('/panel/users/'.$new->id)->assertOk()->assertSee($data['name']);
        $hash = $new->password;
        $this->put('/panel/users/'.$new->id, array_replace($data, ['name' => 'Zmieniony Moderator', 'password' => '', 'password_confirmation' => '']))->assertRedirect('/panel/users');
        $this->assertSame($hash, $new->fresh()->password);
        $this->delete('/panel/users/'.$new->id)->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $new->id]);
        $this->delete('/panel/users/'.$customer->id)->assertRedirect();
        $this->assertNull($order->fresh()->user_id);
        $this->assertDatabaseCount('order_items', 1);
    }

    public function test_last_admin_and_own_account_are_protected(): void
    {
        $admin = $this->user('admin');
        $this->actingAs($admin);
        $this->delete('/panel/users/'.$admin->id)->assertSessionHasErrors('user');
        $this->put('/panel/users/'.$admin->id, ['name' => $admin->name, 'email' => $admin->email, 'role' => 'customer'])->assertSessionHasErrors('role');
        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_moderator_products_crud_validates_sku_and_archives_products(): void
    {
        $this->actingAs($this->user('moderator'));
        $data = ['name' => 'Nowy produkt', 'sku' => 'TEST-99', 'description' => 'Opis testowego produktu', 'category_id' => 1, 'price' => '123.45', 'stock' => 3, 'active' => 1];
        $this->post('/panel/products', $data)->assertRedirect('/panel/products');
        $product = Product::where('sku', 'TEST-99')->firstOrFail();
        $this->assertSame(12345, $product->price_grosze);
        $this->put('/panel/products/'.$product->id, array_replace($data, ['price' => '124.00']))->assertRedirect('/panel/products');
        $this->post('/panel/products', array_replace($data, ['sku' => 'bad sku']))->assertSessionHasErrors('sku');
        $this->delete('/panel/products/'.$product->id)->assertRedirect();
        $this->assertFalse($product->fresh()->active);
        $this->get('/produkty/'.$product->id)->assertNotFound();
        $this->post('/panel/users', ['name' => 'Nie wolno'])->assertForbidden();
    }

    public function test_order_status_can_only_move_forward_by_staff(): void
    {
        $order = $this->createOrder();
        $this->actingAs($this->user())->patch('/panel/orders/'.$order->id, ['status' => 'processing'])->assertForbidden();
        $this->actingAs($this->user('moderator'))->patch('/panel/orders/'.$order->id, ['status' => 'completed'])->assertSessionHasErrors('status');
        $this->patch('/panel/orders/'.$order->id, ['status' => 'processing'])->assertRedirect();
        $this->patch('/panel/orders/'.$order->id, ['status' => 'completed'])->assertRedirect();
        $this->assertSame('completed', $order->fresh()->status);
        $this->patch('/panel/orders/'.$order->id, ['status' => 'processing'])->assertSessionHasErrors('status');
    }

    public function test_html_is_escaped_and_search_injection_does_not_change_database(): void
    {
        $product = Product::first();
        $product->update(['name' => '<script>alert(1)</script>']);
        $this->get('/produkty/'.$product->id)->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->get('/?q='.urlencode("' OR 1=1 --"))->assertOk()->assertSee('Brak produktów');
        $this->assertDatabaseCount('products', 18);
    }

    public function test_nbp_price_and_offline_fallback(): void
    {
        Http::fake(['api.nbp.pl/*' => Http::response(['rates' => [['mid' => 4.0, 'effectiveDate' => '2026-10-06']]])]);
        $product = Product::first();
        $this->get('/produkty/'.$product->id.'/euro')->assertOk()->assertSee('62,48 EUR');
        Http::fake(['api.nbp.pl/*' => Http::failedConnection()]);
        $this->get('/produkty/'.$product->id.'/euro')->assertOk()->assertSee('Cena i zamówienia w PLN działają normalnie');
    }

    public function test_seeder_is_repeatable_without_resetting_stock_or_accounts(): void
    {
        Product::first()->update(['stock' => 1]);
        $this->seed(ShopSeeder::class);
        $this->assertDatabaseCount('users', 3);
        $this->assertDatabaseCount('products', 18);
        $this->assertSame(1, Product::first()->stock);
    }

    public function test_posts_require_csrf(): void
    {
        $this->app->instance('env', 'local');
        $this->post('/koszyk/1', ['quantity' => 1])->assertStatus(419);
        $this->post('/rejestracja', [])->assertStatus(419);
    }

    private function createOrder(): Order
    {
        $product = Product::first();
        $token = (string) Str::uuid();
        $this->actingAs($this->user())->withSession(['cart' => [$product->id => 1], 'checkout_token' => $token]);
        $this->post('/zamowienie', $this->delivery($token))->assertRedirect();

        return Order::latest('id')->firstOrFail();
    }
}
