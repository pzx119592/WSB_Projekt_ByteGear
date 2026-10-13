<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['admin' => 'Administrator', 'moderator' => 'Moderator', 'customer' => 'Klient Demo'] as $role => $name) {
            $demo = User::firstOrCreate(['email' => $role.'@example.test'], ['name' => $name, 'role' => $role, 'password' => 'DemoSklep123!']);
            // Tylko trzy konta demonstracyjne omijają aktywację; nowe konta wymagają e-maila.
            if (! $demo->hasVerifiedEmail()) {
                $demo->markEmailAsVerified();
            }
        }
        $groups = [
            'keyboard' => ['Klawiatury', [['Klawiatura mechaniczna 75% USB-C — grafitowa', 24990], ['Klawiatura cicha z blokiem numerycznym — czarna', 15990], ['Klawiatura mechaniczna TKL — czarna', 34990]]],
            'mouse' => ['Myszy', [['Mysz bezprzewodowa 2,4 GHz — czarna', 11990], ['Mysz gamingowa bezprzewodowa — lekka, czarna', 22990], ['Mysz pionowa ergonomiczna — bezprzewodowa', 17990]]],
            'headset' => ['Słuchawki', [['Słuchawki nauszne z mikrofonem — czarne', 19990], ['Słuchawki nauszne bezprzewodowe — czarne', 29990], ['Słuchawki gamingowe z mikrofonem — czarno-niebieskie', 14990]]],
            'monitor' => ['Monitory', [['Monitor 24″ IPS — HDMI, DisplayPort', 54990], ['Monitor 27″ QHD IPS — regulowana podstawa', 99990], ['Monitor gamingowy 27″ IPS — czarny', 84990]]],
            'pad' => ['Podkładki', [['Podkładka na biurko XL — czarna', 6990], ['Podkładka pod mysz — miękka, czarna', 3990], ['Podkładka na biurko — szara z obszyciem', 9990]]],
            'usb' => ['Akcesoria USB', [['Hub USB-C — USB-A, HDMI, USB-C', 12990], ['Stacja dokująca USB-C 7w1 — czytnik kart SD', 24990], ['Kabel USB-C do USB-C — w oplocie', 4990]]],
        ];
        $descriptions = [
            'keyboard' => "Wygodna klawiatura do codziennej pracy i grania.\nUkład US QWERTY • złącze USB-C • regulowana wysokość • solidna obudowa.",
            'mouse' => "Precyzyjna mysz o wygodnym kształcie.\nŁączność bezprzewodowa 2,4 GHz • regulowane DPI • 6 przycisków • lekka konstrukcja.",
            'headset' => "Słuchawki do muzyki, rozmów i gier.\nPrzetworniki 40 mm • miękkie nauszniki • mikrofon • regulowany pałąk.",
            'monitor' => "Czytelny obraz i więcej przestrzeni na Twoim biurku.\nMatryca IPS • HDMI i DisplayPort • regulacja nachylenia • mocowanie VESA.",
            'pad' => "Podkładka, która porządkuje stanowisko.\nAntypoślizgowy spód • obszyte krawędzie • miękka powierzchnia • łatwe czyszczenie.",
            'usb' => "Praktyczne połączenie Twoich urządzeń.\nUSB-C • kompaktowa obudowa • plug and play • do pracy z komputerem.",
        ];
        foreach ($groups as $slug => [$name, $products]) {
            $category = Category::firstOrCreate(['slug' => $slug], ['name' => $name]);
            foreach ($products as $index => [$title, $price]) {
                Product::firstOrCreate(['sku' => strtoupper($slug).'-'.($index + 1)], [
                    'category_id' => $category->id, 'name' => $title, 'description' => $descriptions[$slug],
                    'price_grosze' => $price, 'stock' => 12 + $index * 3, 'active' => true,
                ]);
            }
        }
    }
}
