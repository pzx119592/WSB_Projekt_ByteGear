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
            'keyboard' => ['Klawiatury', [['BG K75 Mechanical', 24990], ['BG K120 Silent', 15990], ['BG K87 TKL', 34990]]],
            'mouse' => ['Myszy', [['BG M100 Wireless', 11990], ['BG M200 Gaming', 22990], ['BG Ergo V1', 17990]]],
            'headset' => ['Słuchawki', [['BG H100 Studio', 19990], ['BG H200 Wireless', 29990], ['BG H300 Gaming', 14990]]],
            'monitor' => ['Monitory', [['BG V24 IPS', 54990], ['BG V27 QHD', 99990], ['BG V27 Gaming', 84990]]],
            'pad' => ['Podkładki', [['BG DeskMat XL', 6990], ['BG MousePad S', 3990], ['BG DeskMat Pro', 9990]]],
            'usb' => ['Akcesoria USB', [['BG Hub C5', 12990], ['BG Dock C7', 24990], ['BG Cable C100 1m', 4990]]],
        ];
        $descriptions = [
            'KEYBOARD-1' => 'Kompaktowa klawiatura mechaniczna do pracy i grania. Grafitowa obudowa, ciemne klawisze i niebieskie akcenty.',
            'KEYBOARD-2' => 'Smukła klawiatura biurowa z cichymi klawiszami i blokiem numerycznym. Wariant czarny.',
            'KEYBOARD-3' => 'Klawiatura mechaniczna do gier bez bloku numerycznego. Ciemna obudowa i kompaktowy układ klawiszy.',
            'MOUSE-1' => 'Bezprzewodowa mysz do codziennej pracy. Klasyczny kształt, rolka przewijania i matowa obudowa.',
            'MOUSE-2' => 'Lekka mysz gamingowa z przyciskami bocznymi. Ciemna obudowa i niebieski akcent przy rolce.',
            'MOUSE-3' => 'Bezprzewodowa mysz pionowa o ergonomicznym kształcie. Do wygodnej pracy przy komputerze.',
            'HEADSET-1' => 'Przewodowe słuchawki nauszne z mikrofonem na wysięgniku. Miękkie nauszniki i regulowany pałąk.',
            'HEADSET-2' => 'Bezprzewodowe słuchawki nauszne do muzyki i rozmów. Miękkie poduszki i stonowany czarny wygląd.',
            'HEADSET-3' => 'Przewodowy zestaw gamingowy z mikrofonem. Czarna obudowa z niebieskimi akcentami.',
            'MONITOR-1' => 'Monitor biurowy z ekranem IPS 24 cale. Cienkie ramki i stabilna podstawa.',
            'MONITOR-2' => 'Monitor 27 cali o rozdzielczości QHD. Regulowana podstawa i wygodna przestrzeń robocza.',
            'MONITOR-3' => 'Monitor gamingowy 27 cali z cienkimi ramkami i podstawą w kształcie litery V.',
            'PAD-1' => 'Duża, czarna podkładka tekstylna na biurko. Miejsce pod mysz i klawiaturę.',
            'PAD-2' => 'Kompaktowa podkładka pod mysz z miękką, ciemną powierzchnią i zaokrąglonymi narożnikami.',
            'PAD-3' => 'Duża szara podkładka na biurko z obszyciem. Równa powierzchnia do pracy z myszą.',
            'USB-1' => 'Kompaktowy hub USB-C z portami USB-A, USB-C i HDMI. Krótki przewód i grafitowa obudowa.',
            'USB-2' => 'Stacja dokująca USB-C 7w1 z portami USB i czytnikiem kart. Do rozbudowy stanowiska z laptopem.',
            'USB-3' => 'Kabel USB-C do USB-C w czarnym oplocie. Model demonstracyjny o długości 1 m.',
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
