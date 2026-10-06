<?php

return [
    'required' => 'Pole :attribute jest wymagane.', 'string' => 'Pole :attribute musi być tekstem.',
    'email' => 'Podaj poprawny adres e-mail.', 'unique' => 'Ta wartość pola :attribute jest już używana.',
    'confirmed' => 'Potwierdzenie hasła nie jest zgodne.', 'regex' => 'Pole :attribute ma niepoprawny format.',
    'integer' => 'Pole :attribute musi być liczbą całkowitą.', 'numeric' => 'Pole :attribute musi być liczbą.',
    'min' => ['string' => 'Pole :attribute musi mieć co najmniej :min znaków.', 'numeric' => 'Pole :attribute musi wynosić co najmniej :min.'],
    'max' => ['string' => 'Pole :attribute może mieć najwyżej :max znaków.', 'numeric' => 'Pole :attribute może wynosić najwyżej :max.'],
    'gt' => ['numeric' => 'Pole :attribute musi być większe niż :value.'],
    'decimal' => 'Pole :attribute może mieć najwyżej 2 miejsca po przecinku.', 'exists' => 'Wybrana wartość nie istnieje.',
    'in' => 'Wybrana wartość pola :attribute jest nieprawidłowa.', 'uuid' => 'Formularz wygasł. Otwórz go ponownie.',
    'boolean' => 'Niepoprawna wartość pola :attribute.',
    'attributes' => ['name' => 'nazwa', 'email' => 'e-mail', 'password' => 'hasło', 'quantity' => 'liczba sztuk', 'address' => 'adres',
        'postcode' => 'kod pocztowy', 'city' => 'miasto', 'price' => 'cena', 'stock' => 'stan magazynowy', 'category_id' => 'kategoria',
        'description' => 'opis', 'role' => 'rola', 'checkout_token' => 'identyfikator zamówienia'],
];
