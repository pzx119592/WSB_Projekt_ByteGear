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
            'keyboard' => ['Klawiatury', [['Keyline K75', 24990], ['Keyline Silent', 15990], ['Keyline Pro TKL', 34990]]],
            'mouse' => ['Myszy', [['Track M1 Wireless', 11990], ['Track Pro Light', 22990], ['Track Ergo', 17990]]],
            'headset' => ['Słuchawki', [['Sound H1 Studio', 19990], ['Sound Air Wireless', 29990], ['Sound Play', 14990]]],
            'monitor' => ['Monitory', [['View 24 IPS', 54990], ['View 27 QHD', 99990], ['View 27 Fast', 84990]]],
            'pad' => ['Podkładki', [['Desk Mat XL', 6990], ['Desk Mat Soft', 3990], ['Desk Mat Pro', 9990]]],
            'usb' => ['Akcesoria USB', [['Link Hub USB-C', 12990], ['Link Dock 7w1', 24990], ['Link Cable Pro', 4990]]],
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
