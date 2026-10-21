<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    private function titles(): array
    {
        return [
            'KEYBOARD-1' => ['Keyline K75', 'Klawiatura mechaniczna 75% USB-C — grafitowa'],
            'KEYBOARD-2' => ['Keyline Silent', 'Klawiatura cicha z blokiem numerycznym — czarna'],
            'KEYBOARD-3' => ['Keyline Pro TKL', 'Klawiatura mechaniczna TKL — czarna'],
            'MOUSE-1' => ['Track M1 Wireless', 'Mysz bezprzewodowa 2,4 GHz — czarna'],
            'MOUSE-2' => ['Track Pro Light', 'Mysz gamingowa bezprzewodowa — lekka, czarna'],
            'MOUSE-3' => ['Track Ergo', 'Mysz pionowa ergonomiczna — bezprzewodowa'],
            'HEADSET-1' => ['Sound H1 Studio', 'Słuchawki nauszne z mikrofonem — czarne'],
            'HEADSET-2' => ['Sound Air Wireless', 'Słuchawki nauszne bezprzewodowe — czarne'],
            'HEADSET-3' => ['Sound Play', 'Słuchawki gamingowe z mikrofonem — czarno-niebieskie'],
            'MONITOR-1' => ['View 24 IPS', 'Monitor 24″ IPS — HDMI, DisplayPort'],
            'MONITOR-2' => ['View 27 QHD', 'Monitor 27″ QHD IPS — regulowana podstawa'],
            'MONITOR-3' => ['View 27 Fast', 'Monitor gamingowy 27″ IPS — czarny'],
            'PAD-1' => ['Desk Mat XL', 'Podkładka na biurko XL — czarna'],
            'PAD-2' => ['Desk Mat Soft', 'Podkładka pod mysz — miękka, czarna'],
            'PAD-3' => ['Desk Mat Pro', 'Podkładka na biurko — szara z obszyciem'],
            'USB-1' => ['Link Hub USB-C', 'Hub USB-C — USB-A, HDMI, USB-C'],
            'USB-2' => ['Link Dock 7w1', 'Stacja dokująca USB-C 7w1 — czytnik kart SD'],
            'USB-3' => ['Link Cable Pro', 'Kabel USB-C do USB-C — w oplocie'],
        ];
    }

    public function up(): void
    {
        foreach ($this->titles() as $sku => [$old, $new]) {
            // Zachowujemy własne nazwy zmienione w panelu oraz historię order_items.
            DB::table('products')->where('sku', $sku)->where('name', $old)->update(['name' => $new]);
        }
    }

    public function down(): void
    {
        foreach ($this->titles() as $sku => [$old, $new]) {
            DB::table('products')->where('sku', $sku)->where('name', $new)->update(['name' => $old]);
        }
    }
};
