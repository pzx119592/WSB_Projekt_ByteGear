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
            'keyboard' => ['Klawiatury', [['Keychron V1 Max Banana Switch Black', 24990], ['Logitech K120 czarna', 15990], ['Logitech G413 TKL SE', 34990]]],
            'mouse' => ['Myszy', [['Logitech M185 szara', 11990], ['Logitech G305 Lightspeed czarna', 22990], ['Logitech LIFT Grafitowa', 17990]]],
            'headset' => ['Słuchawki', [['HyperX Cloud III — czarny', 19990], ['Logitech Zone Vibe 100 grafit', 29990], ['HyperX Cloud Stinger 2 Core', 14990]]],
            'monitor' => ['Monitory', [['Dell P2422H', 54990], ['Dell P2723D', 99990], ['AOC 27G2SPU', 84990]]],
            'pad' => ['Podkładki', [['SteelSeries QcK XXL', 6990], ['Corsair MM300 Gaming (Small)', 3990], ['SteelSeries QcK Heavy XXL', 9990]]],
            'usb' => ['Akcesoria USB', [['Unitek USB-C — HDMI, 2x USB-C, 2x USB-A', 12990], ['Baseus Hub 7w1 UltraJoy', 24990], ['Baseus CADH000701 USB-C — USB-C 100W 1m czarny', 4990]]],
        ];
        $descriptions = [
            'KEYBOARD-1' => 'Kompaktowa klawiatura mechaniczna Keychron z przełącznikami Banana. Do pracy i grania.',
            'KEYBOARD-2' => 'Przewodowa klawiatura biurowa z pełnym układem klawiszy i blokiem numerycznym.',
            'KEYBOARD-3' => 'Mechaniczna klawiatura gamingowa bez bloku numerycznego, z podświetleniem klawiszy.',
            'MOUSE-1' => 'Bezprzewodowa mysz optyczna do codziennej pracy z komputerem.',
            'MOUSE-2' => 'Bezprzewodowa mysz gamingowa z sensorem optycznym i sześcioma przyciskami.',
            'MOUSE-3' => 'Ergonomiczna mysz pionowa z łącznością bezprzewodową. Wariant grafitowy.',
            'HEADSET-1' => 'Przewodowy zestaw słuchawkowy do gier, z mikrofonem i nauszną konstrukcją.',
            'HEADSET-2' => 'Bezprzewodowe słuchawki nauszne z mikrofonem do rozmów i codziennej pracy.',
            'HEADSET-3' => 'Przewodowe słuchawki gamingowe z mikrofonem i sterowaniem przy słuchawce.',
            'MONITOR-1' => 'Monitor biurowy z matrycą IPS, ekranem 23,8 cala i rozdzielczością Full HD.',
            'MONITOR-2' => 'Monitor z ekranem 27 cali do pracy z dokumentami i codziennego korzystania z komputera.',
            'MONITOR-3' => 'Monitor gamingowy z ekranem 27 cali. Do stanowiska komputerowego gracza.',
            'PAD-1' => 'Tekstylna podkładka o wymiarach 900 × 400 mm, zapewniająca miejsce pod mysz i klawiaturę.',
            'PAD-2' => 'Kompaktowa podkładka pod mysz do stanowiska gamingowego.',
            'PAD-3' => 'Duża podkładka pod mysz z serii QcK Heavy, do rozbudowanego stanowiska komputerowego.',
            'USB-1' => 'Hub USB-C rozszerzający dostępne złącza o HDMI oraz porty USB-C i USB-A.',
            'USB-2' => 'Wielofunkcyjny hub USB-C z serii UltraJoy do podłączania akcesoriów do laptopa.',
            'USB-3' => 'Kabel USB-C do USB-C o długości 1 m, obsługujący zasilanie do 100 W.',
        ];
        foreach ($groups as $slug => [$name, $products]) {
            $category = Category::firstOrCreate(['slug' => $slug], ['name' => $name]);
            foreach ($products as $index => [$title, $price]) {
                Product::firstOrCreate(['sku' => strtoupper($slug).'-'.($index + 1)], [
                    'category_id' => $category->id, 'name' => $title, 'description' => $descriptions[strtoupper($slug).'-'.($index + 1)],
                    'price_grosze' => $price, 'stock' => 12 + $index * 3, 'active' => true,
                ]);
            }
        }
    }
}
