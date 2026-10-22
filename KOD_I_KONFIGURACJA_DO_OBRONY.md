# Kod i konfiguracja do obrony projektu

## Jak korzystać

1. Otwórz folder **`C:\xampp\htdocs\bytegear`** w Visual Studio Code.
2. **Ctrl+P** → wklej ścieżkę pliku → Enter.
3. **Ctrl+F** → wpisz fragment wskazany w danym punkcie.
4. Pokaż działanie w przeglądarce, a potem odpowiadający mu kod.

Ścieżki poniżej są względem folderu projektu. Kliknięcie odnośnika otwiera plik na GitHubie.
Nazwy metod są pewniejsze niż numery linii, które zmieniają się po formatowaniu.
Do wyszukiwania wpisuj np. `function login` lub `Auth::attempt`, bez pustych nawiasów.
Przykłady zawierające `...` to skróty wyjaśnienia, nie tekst do kopiowania do kodu.

- [Scenariusz prezentacji w przeglądarce — PDF](output/pdf/Prezentacja_krok_po_kroku_ByteGear.pdf)
- [Drzewko plików](MAPA_PLIKOW.md)
- [Wymagania zaliczenia](docs/WYMAGANIA.md)

## Spis pytań

1. [Połączenie z bazą](#1-gdzie-jest-połączenie-z-bazą)
2. [Tabele, role i dane demonstracyjne](#2-gdzie-powstają-tabele-i-rola-użytkownika)
3. [Adresy stron](#3-gdzie-są-adresy-stron)
4. [MVC](#4-gdzie-jest-mvc)
5. [HTML i CSS](#5-gdzie-jest-html-i-css)
6. [Rejestracja](#6-gdzie-rejestrujesz-użytkownika)
7. [Hashowanie hasła](#7-gdzie-hashujesz-hasło)
8. [Logowanie i wylogowanie](#8-gdzie-obsługujesz-logowanie-i-wylogowanie)
9. [Uprawnienia i różne strony po logowaniu](#9-gdzie-sprawdzasz-uprawnienia)
10. [CRUD użytkowników](#10-gdzie-jest-crud-użytkowników)
11. [Walidacja i regex](#11-gdzie-walidujesz-dane-i-używasz-regex)
12. [Koszyk w sesji](#12-gdzie-jest-koszyk-w-sesji)
13. [Dodawanie i wycofywanie produktów](#13-gdzie-dodajesz-i-wycofujesz-produkty)
14. [Zapis zamówienia](#14-gdzie-zapisujesz-zamówienie)
15. [SQL Injection](#15-gdzie-jest-ochrona-przed-sql-injection)
16. [CSRF i XSS](#16-gdzie-jest-csrf-i-ochrona-przed-xss)
17. [Poczta i aktywacja](#17-gdzie-konfigurujesz-pocztę-i-aktywację)
18. [Reset hasła](#18-gdzie-jest-reset-hasła)
19. [Nazwa sklepu](#19-gdzie-zmieniasz-nazwę-sklepu)
20. [API i testy](#20-gdzie-jest-api-i-testy)
21. [Profil, zdjęcia, anulowanie i potwierdzenia](#21-gdzie-są-profil-zdjęcia-anulowanie-i-potwierdzenia)

## 1. Gdzie jest połączenie z bazą?

**Otwórz:** lokalny `.env`. Do publicznego pokazu użyj bezpiecznego wzoru [.env.example](.env.example).

**Znajdź:** `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

Domyślne ustawienia projektu to sterownik `mysql`, serwer `127.0.0.1`, port `3306`
i baza `bytegear`. Serwerem jest MariaDB z XAMPP. Ustawienia mogą różnić się na innym komputerze.

**Następnie otwórz:** [config/database.php](config/database.php).
Wyszukaj `'default'` i sekcję `'mysql'`.

**Wyjaśnij:** `.env` zawiera ustawienia tego komputera, a konfiguracja odczytuje je przez `env()`.
Modele korzystają z tego połączenia; nie otwierają własnego `mysqli`.

Po zmianie ustawień wykonaj:

```cmd
php artisan config:clear
```

Nie pokazuj rzeczywistych haseł, `APP_KEY` ani danych SMTP na publicznym nagraniu.
`.env.example` jest wzorem, nie aktywną konfiguracją. `.env` jest pomijany przez `.gitignore`.

## 2. Gdzie powstają tabele i rola użytkownika?

**Otwórz:**

- [Migracja użytkowników](database/migrations/0001_01_01_000000_create_users_table.php) — znajdź `Schema::create`, `email_verified_at`, `password_reset_tokens`, `sessions`.
- [Migracja sklepu](database/migrations/2026_10_06_000001_create_shop_tables.php) — znajdź `Schema::table` i kolumnę `role`; dalej tabele `categories`, `products`, `orders`, `order_items`.
- [ShopSeeder.php](database/seeders/ShopSeeder.php) — metoda `run`, konta demo i przykładowe produkty.

Rola ma domyślną wartość:

```php
$table->string('role')->default('customer');
```

**Wyjaśnij:** migracje opisują strukturę tabel i klucze obce, a seeder dodaje dane początkowe.

```cmd
php artisan migrate
```

Na pustej bazie poniższe polecenie tworzy również konta demo i 18 produktów:

```cmd
php artisan migrate --seed
```

To nie importuje domowych zamówień. Ponowne seedowanie nie resetuje istniejących kont
ani stanów magazynowych. **Nie używaj `migrate:fresh` podczas obrony — usuwa dane.**

## 3. Gdzie są adresy stron?

**Otwórz:** [routes/web.php](routes/web.php).

**Znajdź:** `'/logowanie'`, `'/rejestracja'`, `'/koszyk'`, `'/zamowienie'`.

```php
Route::get('/logowanie', [AuthController::class, 'loginForm'])
    ->name('login');
```

**Wyjaśnij:** trasa przypisuje adres i metodę żądania do obsługi w kontrolerze.
Nie każda trasa oznacza przekierowanie na inny adres.

- `GET` — wyświetlenie strony.
- `POST` — wysłanie formularza, zapis lub inna akcja.
- `PUT` / `PATCH` — zmiana danych.
- `DELETE` — usunięcie lub wycofanie.

`Route::resource('users', ...)` tworzy trasy CRUD. Nazwy nadane przez `name()`
pozwalają budować adresy np. przez `route('login')`.

**Dodatkowo:** [bootstrap/app.php](bootstrap/app.php), fragmenty `withRouting` i `withMiddleware`.
Tam podpięto plik tras i alias zabezpieczenia `role`.

Lista tras w terminalu:

```cmd
php artisan route:list
```

## 4. Gdzie jest MVC?

Pokaż jeden przykład — stronę główną:

| Część | Plik | Co robi |
|---|---|---|
| Model | [Product.php](app/Models/Product.php) | Obsługuje dane produktu, pola i relację `category()` |
| Kontroler | [CatalogController.php](app/Http/Controllers/CatalogController.php) | Metoda `index()` pobiera produkty i przekazuje je do widoku |
| Widok | [catalog/index.blade.php](resources/views/catalog/index.blade.php) | Tworzy HTML katalogu |
| Trasa | [web.php](routes/web.php) | Łączy adres `/` z metodą `index()` |

**Wyjaśnij:** adres trafia przez trasę do kontrolera. Kontroler pyta model o dane,
a widok Blade zamienia przekazane dane w HTML strony.

Modele dziedziczą po `Model`; `User` po `Authenticatable`.
Laravel domyślnie wiąże np. model `Product` z tabelą `products`.

Relacje do pokazania: `Category::products()`, `Product::category()`,
`Order::items()`, `Order::user()`, `OrderItem::order()`.

## 5. Gdzie jest HTML i CSS?

| Co pokazać | Plik | Czego szukać |
|---|---|---|
| Strona główna | [catalog/index.blade.php](resources/views/catalog/index.blade.php) | `Małe zmiany.` |
| Logowanie | [auth/login.blade.php](resources/views/auth/login.blade.php) | `<form` |
| Rejestracja | [auth/register.blade.php](resources/views/auth/register.blade.php) | `<form` |
| Koszyk | [cart.blade.php](resources/views/cart.blade.php) | `@section` |
| Dane dostawy | [checkout.blade.php](resources/views/checkout.blade.php) | `Dokończ zamówienie` |
| Historia zamówień | [orders/index.blade.php](resources/views/orders/index.blade.php) | `@section` |
| Szczegóły zamówienia | [orders/show.blade.php](resources/views/orders/show.blade.php) | `Zamówienie #` |
| Menu i stopka | [layouts/app.blade.php](resources/views/layouts/app.blade.php) | `<header`, `<footer` |
| Kolory i wygląd | [shop.css](public/css/shop.css) | `background`, `font-size`, `.hero h1` |

We wspólnym szablonie pokaż dołączenie CSS:

```blade
<link rel="stylesheet" href="{{ asset('css/shop.css') }}">
```

Widoki korzystają z niego przez:

```blade
@extends('layouts.app')
```

Treść z `@section('content')` trafia do miejsca `@yield('content')` we wspólnym szablonie.

**Wyjaśnij:** HTML opisuje treść i strukturę, CSS wygląd. Menu i stopki nie kopiujemy
do każdego widoku. Grafiki są lokalnie w `public/images/products/*.webp`.
Nie potrzeba kompilacji przez npm.

## 6. Gdzie rejestrujesz użytkownika?

**Formularz:** [auth/register.blade.php](resources/views/auth/register.blade.php) — pokaż `POST`, `@csrf` i pola.

**Trasa:** [web.php](routes/web.php) — znajdź `register.store`.

**Zapis:** [AuthController.php](app/Http/Controllers/AuthController.php) — metoda `register`.

```php
$data['role'] = 'customer';
$user = User::create($data);
$sent = app(AccountMail::class)->verify($user);
```

**Wyjaśnij:** serwer najpierw sprawdza dane przez `validate`, potem wymusza rolę klienta,
tworzy konto i próbuje wysłać aktywację. Przesłanie `role=admin` z formularza nie nadaje tej roli.
Obsługa wysyłki jest w [AccountMail.php](app/Services/AccountMail.php), metoda `verify`.

## 7. Gdzie hashujesz hasło?

**Otwórz:** [User.php](app/Models/User.php), metoda `casts`.

**Znajdź:** `hashed`.

```php
'password' => 'hashed',
```

**Wyjaśnij:** Laravel zapisuje jednokierunkowy hash hasła. Działa to przy rejestracji
i tworzeniu lub edycji konta przez administratora. Nie można odczytać starego hasła z hasha.

Przy logowaniu [AuthController.php](app/Http/Controllers/AuthController.php) używa
`Auth::attempt($data)` do sprawdzenia zgodności hasła. Nie porównujemy jawnych haseł ręcznie.

Ustawienia logowania: [config/auth.php](config/auth.php), sekcje `guards.web` i `providers.users`.
Algorytm można wybrać przez `HASH_DRIVER`; domyślnie Laravel używa bcrypt.
Projekt nie ma własnego `config/hashing.php`; domyślne ustawienia są w
`vendor/laravel/framework/config/hashing.php` po instalacji bibliotek.
`APP_KEY` służy innym mechanizmom szyfrowania i podpisów, nie odwracaniu haseł.

## 8. Gdzie obsługujesz logowanie i wylogowanie?

**Otwórz:** [AuthController.php](app/Http/Controllers/AuthController.php).

| Metoda | Co pokazać |
|---|---|
| `loginForm()` | Zwraca widok `auth.login` |
| `login()` | `Auth::attempt`, `session()->regenerate` |
| `logout()` | `Auth::logout`, `invalidate`, `regenerateToken` |

**Wyjaśnij:** `GET /logowanie` pokazuje formularz, a `POST /logowanie` sprawdza dane.
Po zalogowaniu zmieniamy identyfikator sesji. Wylogowanie unieważnia sesję i regeneruje token CSRF.

- **Uwierzytelnianie** — sprawdzenie tożsamości przez e-mail i hasło.
- **Autoryzacja** — sprawdzenie uprawnień, np. roli administratora; punkt 9.

POST logowania ma limit `throttle:10,1`.
Podpisany link aktywacyjny zapisany w `url.intended` jest kontynuowany po logowaniu;
pozostałe adresy nie zmieniają strony startowej zależnej od roli.

## 9. Gdzie sprawdzasz uprawnienia?

**Otwórz:** [routes/web.php](routes/web.php).

**Znajdź:** `role:admin`.

```php
->middleware('role:admin,moderator')
```

Ta grupa dopuszcza administratora i moderatora do panelu produktów i zamówień.
Zarządzanie użytkownikami ma dodatkowo:

```php
->middleware('role:admin')
```

**Następnie pokaż:** [RequireRole.php](app/Http/Middleware/RequireRole.php), metoda `handle`.

```php
abort_unless($request->user() && in_array($request->user()->role, $roles, true), 403);
```

**Wyjaśnij:** brak dozwolonej roli daje odmowę 403, także po ręcznym wpisaniu adresu.
Samo ukrycie linku w menu nie zabezpiecza panelu.
Alias `role` rejestruje [bootstrap/app.php](bootstrap/app.php).

**Różne strony po logowaniu:** [OrderController.php](app/Http/Controllers/OrderController.php), metoda `dashboard` i `match`:

- Administrator → `admin.users.index`.
- Moderator → `admin.products.index`.
- Klient → widok `account`.

## 10. Gdzie jest CRUD użytkowników?

**Otwórz:** [AdminUserController.php](app/Http/Controllers/AdminUserController.php).

| Działanie | Metody | Ważny fragment |
|---|---|---|
| Lista | `index()` | Pobranie użytkowników |
| Pojedyncza osoba | `show()` | Widok szczegółów |
| Dodanie | `create()` i `store()` | `User::create` |
| Edycja | `edit()` i `update()` | `$user->fill($data)->save()` |
| Usunięcie | `destroy()` | `$user->delete()` |
| Sprawdzenie danych | `data()` | Reguły walidacji |

**HTML:** [lista](resources/views/admin/users/index.blade.php),
[szczegóły](resources/views/admin/users/show.blade.php),
[formularz](resources/views/admin/users/form.blade.php).

**Wyjaśnij:** dostęp ma tylko administrator. Ostatni administrator jest chroniony,
a administrator nie może usunąć siebie. Zmiana e-maila zeruje `email_verified_at`
i wymaga ponownej aktywacji.

## 11. Gdzie walidujesz dane i używasz regex?

**Otwórz:** [AuthController.php](app/Http/Controllers/AuthController.php), metoda `register`.

**Znajdź:** `validate`.

```php
'name' => 'required|string|min:3|max:100',
'email' => 'required|email|max:255|unique:users',
```

- `required` — pole wymagane.
- `email` — format adresu e-mail.
- `unique` — brak duplikatu.
- `min` / `max` — tutaj długość tekstu.
- `confirmed` — zgodność z polem `password_confirmation`.

**Regex do pokazania:**

| Dane | Plik i metoda | Wzór |
|---|---|---|
| Hasło | [AuthController.php](app/Http/Controllers/AuthController.php), `register` | `/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).+$/s` |
| Kod pocztowy | [OrderController.php](app/Http/Controllers/OrderController.php), `store` | `/^[0-9]{2}-[0-9]{3}$/` |
| SKU produktu | [AdminProductController.php](app/Http/Controllers/AdminProductController.php), `data` | `/^[A-Z0-9-]+$/` |

**Wyjaśnij:** wzory wymagają odpowiednio małej i wielkiej litery oraz cyfry,
kodu w formacie `00-000` albo wielkich liter, cyfr i myślników w SKU.
HTML `required` pomaga użytkownikowi; właściwe sprawdzenie danych wykonuje również PHP.

Komunikaty po polsku: [lang/pl/validation.php](lang/pl/validation.php).
Ich wyświetlanie: [layouts/app.blade.php](resources/views/layouts/app.blade.php), `$errors->any()`.
Język: `APP_LOCALE=pl` w `.env`, odczyt przez [config/app.php](config/app.php).

## 12. Gdzie jest koszyk w sesji?

**Otwórz:** [CartController.php](app/Http/Controllers/CartController.php), metody `add`, `update`, `destroy`.

```php
$cart = $request->session()->get('cart', []);
$request->session()->put('cart', $cart);
```

**Wyjaśnij:** odczytujemy koszyk, zmieniamy go i zapisujemy w sesji.
Przechowujemy identyfikatory produktów i liczbę sztuk.

Aktualne ceny i sumy: [app/Services/Cart.php](app/Services/Cart.php), metody `rows` i `total`.
Znajdź `whereIn`, `subtotal`, `array_sum`. Ceny pochodzą z bazy, nie z formularza klienta.

HTML: [cart.blade.php](resources/views/cart.blade.php).
Ustawienie `SESSION_DRIVER=database` jest odczytywane w [config/session.php](config/session.php).
Sesje są przechowywane w tabeli `sessions`, a przeglądarka ma ciasteczko identyfikujące sesję.
**To nadal koszyk w sesji**, mimo że zapis sesji znajduje się w MariaDB.

## 13. Gdzie dodajesz i wycofujesz produkty?

**Otwórz:** [AdminProductController.php](app/Http/Controllers/AdminProductController.php).

- `store()` — `Product::create` dodaje produkt.
- `update()` — `$product->update` zapisuje zmiany.
- `destroy()` — wycofuje produkt:

```php
$product->update(['active' => false]);
```

**Następnie pokaż:** [CatalogController.php](app/Http/Controllers/CatalogController.php), metoda `index`:

```php
$query = Product::with('category')->where('active', true);
```

**Wyjaśnij:** produkt pozostaje w bazie, ale znika z oferty. Można go przywrócić;
historia zamówień zostaje zachowana. Nie jest to fizyczne usunięcie rekordu.

HTML panelu: [lista](resources/views/admin/products/index.blade.php)
i [formularz](resources/views/admin/products/form.blade.php).
Dostęp wymaga roli administratora lub moderatora.
Cenę w formularzu podajemy w złotych, w bazie zapisujemy całkowite grosze.

Zdjęcie wybiera `imagePath()` w [Product.php](app/Models/Product.php).
18 wizualizacji jest przypisanych do SKU; nowy produkt bez własnego zdjęcia otrzymuje
grafikę kategorii. Szczegóły: [ZDJECIA_PRODUKTOW.txt](ZDJECIA_PRODUKTOW.txt).

## 14. Gdzie zapisujesz zamówienie?

**Formularz:** [checkout.blade.php](resources/views/checkout.blade.php).

**Obsługa:** [OrderController.php](app/Http/Controllers/OrderController.php), metody `checkout` i `store`.

**Znajdź i pokaż kolejno:**

1. `validate` — sprawdzenie danych dostawy.
2. `checkout_token` — sprawdzenie tokenu formularza zakupu.
3. `DB::transaction` i `lockForUpdate` — wspólny zapis i blokady produktów.
4. `Order::create` — zapis samego zamówienia.
5. `$order->items()->create` — zapis kupionych produktów, nazw, cen i ilości.
6. `decrement('stock'` — zmniejszenie magazynu.

**Wyjaśnij:** serwer sprawdza koszyk i stan, liczy ceny z bazy, zapisuje zakup i zmniejsza zapas.
Błąd wewnątrz transakcji wycofuje jej zmiany. Blokady chronią przed jednoczesnym zakupem
tych samych ostatnich sztuk. Token i `block()` ograniczają powtórne zapisanie zakupu.

Nazwy i ceny są kopiowane do `order_items`, więc późniejsza edycja produktu
nie zmienia historii zamówienia. Po zakupie koszyk jest czyszczony.

HTML historii: [orders/index.blade.php](resources/views/orders/index.blade.php).
HTML szczegółów: [orders/show.blade.php](resources/views/orders/show.blade.php).
Metoda `show()` sprawdza właściciela lub rolę administratora/moderatora.
Statusy są w [Order.php](app/Models/Order.php), stała `STATUSES`;
przechodzenie do kolejnych etapów obsługuje `update()` w [AdminOrderController.php](app/Http/Controllers/AdminOrderController.php).

## 15. Gdzie jest ochrona przed SQL Injection?

**Otwórz:** [CatalogController.php](app/Http/Controllers/CatalogController.php), metoda `index`.

```php
$query->where('name', 'like', '%'.$filters['q'].'%');
```

**Wyjaśnij:** Eloquent przekazuje tekst jako parametr zapytania.
Sklejanie znaków `%` w wartości parametru nie skleja danych użytkownika z treścią SQL.

**Pokaż też:** `match` wybierający kolumnę sortowania i regułę
`in:new,price_asc,price_desc,name`. Nazwa kolumny jest wybierana z zamkniętej listy,
a nie dowolnego tekstu wpisanego przez użytkownika.

Logowanie używa `Auth::attempt`, zapis metod Eloquent — nie ręcznie sklejanego SQL.
To osobne zabezpieczenie od CSRF i ucieczki HTML.

Testy w [ShopTest.php](tests/Feature/ShopTest.php):

- `test_html_is_escaped_and_search_injection_does_not_change_database`
- `test_catalog_filter_sort_and_brand_configuration`

## 16. Gdzie jest CSRF i ochrona przed XSS?

**CSRF:** pokaż [formularz rejestracji](resources/views/auth/register.blade.php) i dyrektywę:

```blade
@csrf
```

Formularze zmiany i usuwania używają też `@method('PUT')`, `@method('PATCH')`
lub `@method('DELETE')` wraz z `POST`. Trasy web mają ochronę sprawdzającą token.
Test: `test_posts_require_csrf` w [ShopTest.php](tests/Feature/ShopTest.php).

**XSS:** pokaż [widok produktu](resources/views/catalog/show.blade.php)
lub [wspólny szablon](resources/views/layouts/app.blade.php). Zapis `{{ ... }}` ucieka HTML.

**Wyjaśnij:** CSRF ogranicza wykonanie niechcianej akcji w sesji użytkownika.
Ucieczka HTML ogranicza wykonanie wstrzykniętego kodu w stronie.

## 17. Gdzie konfigurujesz pocztę i aktywację?

**Ustawienia:** `MAIL_*` i `APP_URL` w `.env`, odczyt przez [config/mail.php](config/mail.php).
Lokalnie używamy SMTP `127.0.0.1:1025`, `MAIL_MAILER=smtp` i skrzynki WWW
[Mailpit na porcie 8025](http://127.0.0.1:8025/).

**Pokaż kod:**

- [User.php](app/Models/User.php) — `implements MustVerifyEmail`, `email_verified_at`.
- [AccountMail.php](app/Services/AccountMail.php) — `verify`, `sendEmailVerificationNotification`.
- [VerificationController.php](app/Http/Controllers/VerificationController.php) — `notice`, `verify`, `send`; `fulfill` zapisuje potwierdzenie.
- [web.php](routes/web.php) — `verification.verify`, middleware `signed` i grupa `verified`.
- [AppServiceProvider.php](app/Providers/AppServiceProvider.php) — `VerifyEmail::toMailUsing`, polska treść wiadomości.
- [verify-email.blade.php](resources/views/auth/verify-email.blade.php) — HTML oczekiwania na aktywację.
- [Start-Mailpit.ps1](tools/Start-Mailpit.ps1) — lokalny serwer poczty.

**Wyjaśnij:** podpis chroni link przed zmianą. Żądanie sprawdza właściciela linku,
a `verified` blokuje chronione strony niepotwierdzonego konta.
Link aktywacyjny ma domyślnie 60 minut ważności; `expire` z `config/auth.php` dotyczy resetu hasła.
`MAIL_FROM_ADDRESS` i `MAIL_FROM_NAME` określają nadawcę, `APP_URL` adres sklepu w linku.

Mailpit odbiera wiadomości lokalnie. Dostarczenie do zewnętrznej skrzynki wymaga własnego SMTP.
Konfiguracja: [POCZTA_SMTP_LUB_MAILPIT.txt](POCZTA_SMTP_LUB_MAILPIT.txt).

## 18. Gdzie jest reset hasła?

**Otwórz:** [PasswordController.php](app/Http/Controllers/PasswordController.php), metody `email` i `update`.

**Znajdź:** `Password::sendResetLink`, `Password::reset`, `Hash::make`.

**Wyjaśnij:** broker Laravel tworzy i sprawdza jednorazowy token. W bazie jest hash tokenu,
nie jego jawna wartość. Po zmianie hasła usuwamy stare sesje i zmieniamy `remember_token`.

Ustawienia w [config/auth.php](config/auth.php), sekcja `passwords.users`:

- `expire = 60` — ważność tokenu w minutach.
- `throttle = 60` — odstęp tworzenia nowych tokenów w sekundach.

Tokeny są w tabeli `password_reset_tokens`.
Polska treść wiadomości: [AppServiceProvider.php](app/Providers/AppServiceProvider.php), `ResetPassword::toMailUsing`.
HTML formularzy: [forgot-password.blade.php](resources/views/auth/forgot-password.blade.php)
i [reset-password.blade.php](resources/views/auth/reset-password.blade.php).
**Reset hasła nie zastępuje aktywacji e-maila.**

## 19. Gdzie zmieniasz nazwę sklepu?

**Otwórz:** lokalny `.env` i znajdź `APP_NAME`.

```dotenv
APP_NAME="Moja nowa nazwa"
```

Wykonaj `php artisan config:clear` i odśwież stronę.
[config/app.php](config/app.php) odczytuje nazwę, a [wspólny szablon](resources/views/layouts/app.blade.php)
i wiadomości używają `config('app.name')`.
Dla przyszłych pobrań zmień też `.env.example`. Nie trzeba zmieniać folderu ani tabel.

## 20. Gdzie jest API i testy?

**API:** [CatalogController.php](app/Http/Controllers/CatalogController.php), metoda `euro`, `timeout(8)`.
HTML: [catalog/euro.blade.php](resources/views/catalog/euro.blade.php).

**Wyjaśnij:** pobieramy kurs EUR z API NBP. Brak Internetu pokazuje komunikat,
ale nie blokuje zamawiania w PLN.

**Testy:**

- [ShopTest.php](tests/Feature/ShopTest.php) — główne funkcje i błędne przypadki.
- [AccountSecurityTest.php](tests/Feature/AccountSecurityTest.php) — aktywacja, podpis, reset, wygasanie i sesje.
- [ShopExtensionsTest.php](tests/Feature/ShopExtensionsTest.php) — profil, zdjęcia, anulowanie i potwierdzenia.
- [phpunit.xml](phpunit.xml) — SQLite `:memory:`, oddzielnie od domowej MariaDB.

```cmd
php artisan test
```

Ostatni sprawdzony wynik tej wersji: **41 testów, 310 asercji**.
Wersje bibliotek: [composer.json](composer.json) i [composer.lock](composer.lock).
Na drugim komputerze odtwarza je `composer install`.

## 21. Gdzie są profil, zdjęcia, anulowanie i potwierdzenia?

### Profil

- [ProfileController.php](app/Http/Controllers/ProfileController.php) — `edit`, `update`, `password`.
- [auth/profile.blade.php](resources/views/auth/profile.blade.php) — HTML pod adresem `/profil`.

Zmiana imienia nie wymaga hasła. Zmiana e-maila wymaga obecnego hasła i ponownej aktywacji.
Do aktywacji panel zakupów jest zablokowany, ale profil pozwala poprawić adres.
Nowe hasło musi być inne niż obecne; pozostałe sesje są usuwane, bieżąca pozostaje.

### Zdjęcia

- [AdminProductController.php](app/Http/Controllers/AdminProductController.php) — `data`, `store`, `update`.
- [ProductImage.php](app/Services/ProductImage.php) — `store`, zapis JPG przez GD bez EXIF.
- [CatalogController.php](app/Http/Controllers/CatalogController.php) — `image`, udostępnienie pliku.
- [Product.php](app/Models/Product.php) — `imagePath`, wybór zdjęcia.

Własne zdjęcia są w `storage/app/private/product-images`.
Grafiki demo WebP przygotowuje [Optimize-Demo-Images.php](tools/Optimize-Demo-Images.php);
oryginalne PNG pozostają w repozytorium. Szczegóły: [instrukcja zdjęć](ZDJECIA_PRODUKTOW.txt).

### Anulowanie

**Otwórz:** [OrderController.php](app/Http/Controllers/OrderController.php), metoda `cancel`.

**Pokaż:** właściciel, wymagane potwierdzenie, `DB::transaction`, `lockForUpdate`,
`increment('stock'`, status `cancelled` i data `cancelled_at`.

Anulowanie dotyczy własnego nowego zamówienia. Sprawdzenie statusu pod blokadą
chroni przed dwukrotnym zwrotem towaru. Zamówienie i pozycje pozostają w historii.

### Potwierdzenie zakupu

- [OrderMail.php](app/Services/OrderMail.php) — metoda `send`.
- [OrderConfirmation.php](app/Notifications/OrderConfirmation.php) — powiadomienie.
- [mail/order-confirmation.blade.php](resources/views/mail/order-confirmation.blade.php) — HTML wiadomości.

Błąd SMTP nie cofa zakupu. `orders.confirmation_sent_at` pozwala rozpoznać skuteczną wysyłkę
i ponowić nieudaną. Po skutecznym wysłaniu aplikacja nie wysyła ponownie tego samego potwierdzenia.
Nowe kolumny dodaje [migracja zdjęć i anulowania](database/migrations/2026_10_13_000003_add_product_images_and_order_cancellation.php).

Pozostałe elementy wyglądu: [polska paginacja](resources/views/pagination/simple.blade.php)
i [shop.css](public/css/shop.css) — układ mobilny, fokus i statusy.

## Jak ćwiczyć przed obroną

Wybierz działanie, pokaż je w przeglądarce i znajdź odpowiadający mu kod.
Wyjaśnij własnymi słowami: **adres lub formularz → sprawdzenie danych i uprawnień →
odczyt lub zapis w bazie → wynik na stronie**.

Nie ucz się numerów linii. Ucz się, w którym pliku szukać i za co odpowiada wskazany fragment.
