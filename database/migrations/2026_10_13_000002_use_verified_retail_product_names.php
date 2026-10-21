<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    private function entries(): array
    {
        return [
            'KEYBOARD-1' => [
                'Klawiatura mechaniczna 75% USB-C — grafitowa',
                'Keychron V1 Max Banana Switch Black',
                'Kompaktowa klawiatura mechaniczna Keychron z przełącznikami Banana. Do pracy i grania.',
                'Wygodna klawiatura do codziennej pracy i grania.
Układ US QWERTY • złącze USB-C • regulowana wysokość • solidna obudowa.'
            ],
            'KEYBOARD-2' => [
                'Klawiatura cicha z blokiem numerycznym — czarna',
                'Logitech K120 czarna',
                'Przewodowa klawiatura biurowa z pełnym układem klawiszy i blokiem numerycznym.',
                'Wygodna klawiatura do codziennej pracy i grania.
Układ US QWERTY • złącze USB-C • regulowana wysokość • solidna obudowa.'
            ],
            'KEYBOARD-3' => [
                'Klawiatura mechaniczna TKL — czarna',
                'Logitech G413 TKL SE',
                'Mechaniczna klawiatura gamingowa bez bloku numerycznego, z podświetleniem klawiszy.',
                'Wygodna klawiatura do codziennej pracy i grania.
Układ US QWERTY • złącze USB-C • regulowana wysokość • solidna obudowa.'
            ],
            'MOUSE-1' => [
                'Mysz bezprzewodowa 2,4 GHz — czarna',
                'Logitech M185 szara',
                'Bezprzewodowa mysz optyczna do codziennej pracy z komputerem.',
                'Precyzyjna mysz o wygodnym kształcie.
Łączność bezprzewodowa 2,4 GHz • regulowane DPI • 6 przycisków • lekka konstrukcja.'
            ],
            'MOUSE-2' => [
                'Mysz gamingowa bezprzewodowa — lekka, czarna',
                'Logitech G305 Lightspeed czarna',
                'Bezprzewodowa mysz gamingowa z sensorem optycznym i sześcioma przyciskami.',
                'Precyzyjna mysz o wygodnym kształcie.
Łączność bezprzewodowa 2,4 GHz • regulowane DPI • 6 przycisków • lekka konstrukcja.'
            ],
            'MOUSE-3' => [
                'Mysz pionowa ergonomiczna — bezprzewodowa',
                'Logitech LIFT Grafitowa',
                'Ergonomiczna mysz pionowa z łącznością bezprzewodową. Wariant grafitowy.',
                'Precyzyjna mysz o wygodnym kształcie.
Łączność bezprzewodowa 2,4 GHz • regulowane DPI • 6 przycisków • lekka konstrukcja.'
            ],
            'HEADSET-1' => [
                'Słuchawki nauszne z mikrofonem — czarne',
                'HyperX Cloud III — czarny',
                'Przewodowy zestaw słuchawkowy do gier, z mikrofonem i nauszną konstrukcją.',
                'Słuchawki do muzyki, rozmów i gier.
Przetworniki 40 mm • miękkie nauszniki • mikrofon • regulowany pałąk.'
            ],
            'HEADSET-2' => [
                'Słuchawki nauszne bezprzewodowe — czarne',
                'Logitech Zone Vibe 100 grafit',
                'Bezprzewodowe słuchawki nauszne z mikrofonem do rozmów i codziennej pracy.',
                'Słuchawki do muzyki, rozmów i gier.
Przetworniki 40 mm • miękkie nauszniki • mikrofon • regulowany pałąk.'
            ],
            'HEADSET-3' => [
                'Słuchawki gamingowe z mikrofonem — czarno-niebieskie',
                'HyperX Cloud Stinger 2 Core',
                'Przewodowe słuchawki gamingowe z mikrofonem i sterowaniem przy słuchawce.',
                'Słuchawki do muzyki, rozmów i gier.
Przetworniki 40 mm • miękkie nauszniki • mikrofon • regulowany pałąk.'
            ],
            'MONITOR-1' => [
                'Monitor 24″ IPS — HDMI, DisplayPort',
                'Dell P2422H',
                'Monitor biurowy z matrycą IPS, ekranem 23,8 cala i rozdzielczością Full HD.',
                'Czytelny obraz i więcej przestrzeni na Twoim biurku.
Matryca IPS • HDMI i DisplayPort • regulacja nachylenia • mocowanie VESA.'
            ],
            'MONITOR-2' => [
                'Monitor 27″ QHD IPS — regulowana podstawa',
                'Dell P2723D',
                'Monitor z ekranem 27 cali do pracy z dokumentami i codziennego korzystania z komputera.',
                'Czytelny obraz i więcej przestrzeni na Twoim biurku.
Matryca IPS • HDMI i DisplayPort • regulacja nachylenia • mocowanie VESA.'
            ],
            'MONITOR-3' => [
                'Monitor gamingowy 27″ IPS — czarny',
                'AOC 27G2SPU',
                'Monitor gamingowy z ekranem 27 cali. Do stanowiska komputerowego gracza.',
                'Czytelny obraz i więcej przestrzeni na Twoim biurku.
Matryca IPS • HDMI i DisplayPort • regulacja nachylenia • mocowanie VESA.'
            ],
            'PAD-1' => [
                'Podkładka na biurko XL — czarna',
                'SteelSeries QcK XXL',
                'Tekstylna podkładka o wymiarach 900 × 400 mm, zapewniająca miejsce pod mysz i klawiaturę.',
                'Podkładka, która porządkuje stanowisko.
Antypoślizgowy spód • obszyte krawędzie • miękka powierzchnia • łatwe czyszczenie.'
            ],
            'PAD-2' => [
                'Podkładka pod mysz — miękka, czarna',
                'Corsair MM300 Gaming (Small)',
                'Kompaktowa podkładka pod mysz do stanowiska gamingowego.',
                'Podkładka, która porządkuje stanowisko.
Antypoślizgowy spód • obszyte krawędzie • miękka powierzchnia • łatwe czyszczenie.'
            ],
            'PAD-3' => [
                'Podkładka na biurko — szara z obszyciem',
                'SteelSeries QcK Heavy XXL',
                'Duża podkładka pod mysz z serii QcK Heavy, do rozbudowanego stanowiska komputerowego.',
                'Podkładka, która porządkuje stanowisko.
Antypoślizgowy spód • obszyte krawędzie • miękka powierzchnia • łatwe czyszczenie.'
            ],
            'USB-1' => [
                'Hub USB-C — USB-A, HDMI, USB-C',
                'Unitek USB-C — HDMI, 2x USB-C, 2x USB-A',
                'Hub USB-C rozszerzający dostępne złącza o HDMI oraz porty USB-C i USB-A.',
                'Praktyczne połączenie Twoich urządzeń.
USB-C • kompaktowa obudowa • plug and play • do pracy z komputerem.'
            ],
            'USB-2' => [
                'Stacja dokująca USB-C 7w1 — czytnik kart SD',
                'Baseus Hub 7w1 UltraJoy',
                'Wielofunkcyjny hub USB-C z serii UltraJoy do podłączania akcesoriów do laptopa.',
                'Praktyczne połączenie Twoich urządzeń.
USB-C • kompaktowa obudowa • plug and play • do pracy z komputerem.'
            ],
            'USB-3' => [
                'Kabel USB-C do USB-C — w oplocie',
                'Baseus CADH000701 USB-C — USB-C 100W 1m czarny',
                'Kabel USB-C do USB-C o długości 1 m, obsługujący zasilanie do 100 W.',
                'Praktyczne połączenie Twoich urządzeń.
USB-C • kompaktowa obudowa • plug and play • do pracy z komputerem.'
            ],
        ];
    }

    public function up(): void
    {
        foreach ($this->entries() as $sku => [$old, $new, $description, $oldDescription]) {
            $query = DB::table('products')->where('sku', $sku)->where('name', $old);
            $product = $query->first();
            if ($product) {
                $values = ['name' => $new];
                if ($product->description === $oldDescription) {
                    $values['description'] = $description;
                }
                $query->update($values);
            }
        }
    }

    public function down(): void
    {
        foreach ($this->entries() as $sku => [$old, $new, $description, $oldDescription]) {
            $query = DB::table('products')->where('sku', $sku)->where('name', $new);
            $product = $query->first();
            if ($product) {
                $values = ['name' => $old];
                if ($product->description === $description) {
                    $values['description'] = $oldDescription;
                }
                $query->update($values);
            }
        }
    }
};
