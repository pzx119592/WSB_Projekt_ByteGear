<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function entries(): array
    {
        return [
            'KEYBOARD-1' => ['Keychron V1 Max Banana Switch Black', 'BG K75 Mechanical', 'Kompaktowa klawiatura mechaniczna Keychron z przełącznikami Banana. Do pracy i grania.', 'Kompaktowa klawiatura mechaniczna do pracy i grania. Grafitowa obudowa, ciemne klawisze i niebieskie akcenty.'],
            'KEYBOARD-2' => ['Logitech K120 czarna', 'BG K120 Silent', 'Przewodowa klawiatura biurowa z pełnym układem klawiszy i blokiem numerycznym.', 'Smukła klawiatura biurowa z cichymi klawiszami i blokiem numerycznym. Wariant czarny.'],
            'KEYBOARD-3' => ['Logitech G413 TKL SE', 'BG K87 TKL', 'Mechaniczna klawiatura gamingowa bez bloku numerycznego, z podświetleniem klawiszy.', 'Klawiatura mechaniczna do gier bez bloku numerycznego. Ciemna obudowa i kompaktowy układ klawiszy.'],
            'MOUSE-1' => ['Logitech M185 szara', 'BG M100 Wireless', 'Bezprzewodowa mysz optyczna do codziennej pracy z komputerem.', 'Bezprzewodowa mysz do codziennej pracy. Klasyczny kształt, rolka przewijania i matowa obudowa.'],
            'MOUSE-2' => ['Logitech G305 Lightspeed czarna', 'BG M200 Gaming', 'Bezprzewodowa mysz gamingowa z sensorem optycznym i sześcioma przyciskami.', 'Lekka mysz gamingowa z przyciskami bocznymi. Ciemna obudowa i niebieski akcent przy rolce.'],
            'MOUSE-3' => ['Logitech LIFT Grafitowa', 'BG Ergo V1', 'Ergonomiczna mysz pionowa z łącznością bezprzewodową. Wariant grafitowy.', 'Bezprzewodowa mysz pionowa o ergonomicznym kształcie. Do wygodnej pracy przy komputerze.'],
            'HEADSET-1' => ['HyperX Cloud III — czarny', 'BG H100 Studio', 'Przewodowy zestaw słuchawkowy do gier, z mikrofonem i nauszną konstrukcją.', 'Przewodowe słuchawki nauszne z mikrofonem na wysięgniku. Miękkie nauszniki i regulowany pałąk.'],
            'HEADSET-2' => ['Logitech Zone Vibe 100 grafit', 'BG H200 Wireless', 'Bezprzewodowe słuchawki nauszne z mikrofonem do rozmów i codziennej pracy.', 'Bezprzewodowe słuchawki nauszne do muzyki i rozmów. Miękkie poduszki i stonowany czarny wygląd.'],
            'HEADSET-3' => ['HyperX Cloud Stinger 2 Core', 'BG H300 Gaming', 'Przewodowe słuchawki gamingowe z mikrofonem i sterowaniem przy słuchawce.', 'Przewodowy zestaw gamingowy z mikrofonem. Czarna obudowa z niebieskimi akcentami.'],
            'MONITOR-1' => ['Dell P2422H', 'BG V24 IPS', 'Monitor biurowy z matrycą IPS, ekranem 23,8 cala i rozdzielczością Full HD.', 'Monitor biurowy z ekranem IPS 24 cale. Cienkie ramki i stabilna podstawa.'],
            'MONITOR-2' => ['Dell P2723D', 'BG V27 QHD', 'Monitor z ekranem 27 cali do pracy z dokumentami i codziennego korzystania z komputera.', 'Monitor 27 cali o rozdzielczości QHD. Regulowana podstawa i wygodna przestrzeń robocza.'],
            'MONITOR-3' => ['AOC 27G2SPU', 'BG V27 Gaming', 'Monitor gamingowy z ekranem 27 cali. Do stanowiska komputerowego gracza.', 'Monitor gamingowy 27 cali z cienkimi ramkami i podstawą w kształcie litery V.'],
            'PAD-1' => ['SteelSeries QcK XXL', 'BG DeskMat XL', 'Tekstylna podkładka o wymiarach 900 × 400 mm, zapewniająca miejsce pod mysz i klawiaturę.', 'Duża, czarna podkładka tekstylna na biurko. Miejsce pod mysz i klawiaturę.'],
            'PAD-2' => ['Corsair MM300 Gaming (Small)', 'BG MousePad S', 'Kompaktowa podkładka pod mysz do stanowiska gamingowego.', 'Kompaktowa podkładka pod mysz z miękką, ciemną powierzchnią i zaokrąglonymi narożnikami.'],
            'PAD-3' => ['SteelSeries QcK Heavy XXL', 'BG DeskMat Pro', 'Duża podkładka pod mysz z serii QcK Heavy, do rozbudowanego stanowiska komputerowego.', 'Duża szara podkładka na biurko z obszyciem. Równa powierzchnia do pracy z myszą.'],
            'USB-1' => ['Unitek USB-C — HDMI, 2x USB-C, 2x USB-A', 'BG Hub C5', 'Hub USB-C rozszerzający dostępne złącza o HDMI oraz porty USB-C i USB-A.', 'Kompaktowy hub USB-C z portami USB-A, USB-C i HDMI. Krótki przewód i grafitowa obudowa.'],
            'USB-2' => ['Baseus Hub 7w1 UltraJoy', 'BG Dock C7', 'Wielofunkcyjny hub USB-C z serii UltraJoy do podłączania akcesoriów do laptopa.', 'Stacja dokująca USB-C 7w1 z portami USB i czytnikiem kart. Do rozbudowy stanowiska z laptopem.'],
            'USB-3' => ['Baseus CADH000701 USB-C — USB-C 100W 1m czarny', 'BG Cable C100 1m', 'Kabel USB-C do USB-C o długości 1 m, obsługujący zasilanie do 100 W.', 'Kabel USB-C do USB-C w czarnym oplocie. Model demonstracyjny o długości 1 m.'],
        ];
    }

    public function up(): void
    {
        foreach ($this->entries() as $sku => [$old, $new, $oldDescription, $newDescription]) {
            $query = DB::table('products')->where('sku', $sku)->where('name', $old);
            $product = $query->first();
            if ($product) {
                $values = ['name' => $new];
                if ($product->description === $oldDescription) {
                    $values['description'] = $newDescription;
                }
                $query->update($values);
            }
        }
    }

    public function down(): void
    {
        foreach ($this->entries() as $sku => [$old, $new, $oldDescription, $newDescription]) {
            $query = DB::table('products')->where('sku', $sku)->where('name', $new);
            $product = $query->first();
            if ($product) {
                $values = ['name' => $old];
                if ($product->description === $newDescription) {
                    $values['description'] = $oldDescription;
                }
                $query->update($values);
            }
        }
    }
};
