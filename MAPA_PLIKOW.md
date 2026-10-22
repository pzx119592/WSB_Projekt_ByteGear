# Mapa plików sklepu — gdzie czego szukać

Folder projektu u siebie: **`C:\xampp\htdocs\bytegear`**.
Drzewko pokazuje najważniejsze miejsca; nie wymienia każdego pliku Laravel.

**Jak znaleźć plik:** otwórz folder `bytegear` w VS Code → **Ctrl+P** → wklej ścieżkę
zaczynającą się np. od `resources/views/` → Enter. **Ctrl+F** wyszukuje tekst wewnątrz
otwartego pliku, a **Ctrl+Shift+F** we wszystkich plikach projektu.

## 1. Wygląd i treść stron — tutaj jest HTML i CSS

Pliki `.blade.php` zawierają HTML z dodatkami Laravel. Strona jest składana
z treści konkretnego widoku oraz wspólnego menu, nagłówka i stopki.

```text
bytegear/
├── resources/
│   └── views/                         ← HTML poszczególnych stron
│       ├── layouts/
│       │   └── app.blade.php          ← wspólne menu, nazwa sklepu, stopka, podpięcie CSS
│       ├── catalog/
│       │   ├── index.blade.php        ← STRONA GŁÓWNA: nagłówek, filtry, lista produktów
│       │   ├── show.blade.php         ← szczegóły jednego produktu
│       │   └── euro.blade.php         ← cena przeliczona na EUR z kursu NBP
│       ├── auth/
│       │   ├── login.blade.php        ← LOGOWANIE: pola e-mail i hasło
│       │   ├── register.blade.php     ← rejestracja nowego konta
│       │   ├── verify-email.blade.php ← prośba o potwierdzenie adresu e-mail
│       │   ├── profile.blade.php      ← zmiana danych i hasła w swoim profilu
│       │   ├── forgot-password.blade.php ← formularz „Nie pamiętam hasła”
│       │   └── reset-password.blade.php  ← ustawienie hasła po kliknięciu linku
│       ├── admin/
│       │   ├── users/
│       │   │   ├── index.blade.php    ← lista użytkowników dla administratora
│       │   │   ├── show.blade.php     ← szczegóły jednego użytkownika
│       │   │   └── form.blade.php     ← dodawanie i edycja użytkownika
│       │   └── products/
│       │       ├── index.blade.php    ← lista produktów dla pracownika
│       │       └── form.blade.php     ← dodawanie/edycja produktu i zdjęcia
│       ├── orders/
│       │   ├── index.blade.php        ← lista zamówień (klient lub pracownik)
│       │   └── show.blade.php         ← szczegóły, status, anulowanie zamówienia
│       ├── mail/
│       │   └── order-confirmation.blade.php ← treść e-maila z potwierdzeniem zakupu
│       ├── pagination/
│       │   └── simple.blade.php       ← przyciski przechodzenia między stronami list
│       ├── account.blade.php          ← „Moje konto” klienta
│       ├── cart.blade.php             ← koszyk: pozycje, ilości, suma
│       ├── checkout.blade.php         ← adres dostawy i przycisk złożenia zamówienia
│       └── welcome.blade.php          ← domyślny widok Laravel; NIE jest stroną sklepu
└── public/
    ├── css/
    │   └── shop.css                   ← WYGLĄD: kolory, czcionki, odstępy, układ mobilny
    └── images/
        └── products/                 ← lokalne grafiki produktów demonstracyjnych
```

**Przykład:** chcesz zmienić napis „Małe zmiany. Większy komfort.” — otwierasz
`resources/views/catalog/index.blade.php` i szukasz `Małe zmiany.`.
Chcesz zmienić jego rozmiar — w `public/css/shop.css` szukasz `.hero h1`.
Chcesz zmienić menu — otwierasz `resources/views/layouts/app.blade.php` i szukasz `<nav`.
CSS jest już rozpisany na czytelne bloki. **Alt+Z** w VS Code dodatkowo włącza zawijanie długich linii.

## 2. Działanie sklepu — co się dzieje po kliknięciu

Kontroler to plik PHP obsługujący działanie, np. sprawdzenie formularza lub zapis zakupu.
Nie myl go z widokiem: przycisk widzisz w HTML, a jego obsługa jest w kontrolerze.

```text
bytegear/
├── routes/
│   └── web.php                        ← adresy stron i wskazanie, co je obsługuje
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── CatalogController.php  ← produkty, wyszukiwanie, filtry, kurs EUR
│   │   │   ├── AuthController.php     ← rejestracja, logowanie, wylogowanie
│   │   │   ├── VerificationController.php ← potwierdzenie e-maila
│   │   │   ├── PasswordController.php ← odzyskanie zapomnianego hasła
│   │   │   ├── ProfileController.php  ← zmiana własnych danych i hasła
│   │   │   ├── CartController.php     ← dodanie do koszyka, zmiana ilości, usunięcie
│   │   │   ├── OrderController.php    ← zakup, historia klienta, anulowanie, wybór panelu
│   │   │   ├── AdminUserController.php ← zarządzanie użytkownikami
│   │   │   ├── AdminProductController.php ← zarządzanie produktami
│   │   │   └── AdminOrderController.php ← lista zamówień pracownika i zmiana statusów
│   │   └── Middleware/
│   │       └── RequireRole.php        ← sprawdza rolę; odmawia dostępu bez uprawnień
│   ├── Models/                        ← sposób pracy z danymi zapisanymi w bazie
│   │   ├── User.php                   ← użytkownik, rola, HASHOWANIE hasła
│   │   ├── Product.php                ← produkt, kategoria, wybór zdjęcia
│   │   ├── Category.php               ← kategoria produktów
│   │   ├── Order.php                  ← zamówienie i dostępne statusy
│   │   └── OrderItem.php              ← pojedyncza pozycja kupionego zamówienia
│   ├── Services/                      ← pomocnicze działania wydzielone do osobnych plików
│   │   ├── Cart.php                   ← pobranie aktualnych cen i liczenie sum koszyka
│   │   ├── AccountMail.php            ← wysyłka aktywacji konta
│   │   ├── OrderMail.php              ← wysyłka potwierdzenia zakupu
│   │   └── ProductImage.php           ← przetwarzanie i zapis dodanego zdjęcia
│   ├── Notifications/
│   │   └── OrderConfirmation.php      ← przygotowanie wiadomości o zamówieniu
│   └── Providers/
│       └── AppServiceProvider.php     ← polskie treści e-maili aktywacji i resetu hasła
└── bootstrap/
    └── app.php                        ← podpięcie tras i zabezpieczenia sprawdzającego rolę
```

Najczęstsze nazwy w kontrolerach:

- `index` — lista; `show` — szczegóły jednego wpisu.
- `create` — formularz dodawania; `store` — zapis nowego wpisu.
- `edit` — formularz edycji; `update` — zapis zmian.
- `destroy` — usunięcie albo wycofanie z oferty, zależnie od funkcji.
- `validate` — sprawdzenie danych przed zapisem.

**Przykład logowania:** adres w `routes/web.php` → formularz
`resources/views/auth/login.blade.php` → sprawdzenie przez `AuthController.php`
→ konto opisane w `app/Models/User.php`.

## 3. Ustawienia, baza i dane

```text
bytegear/
├── .env                               ← aktywne ustawienia TEGO komputera; poza GitHub
├── .env.example                       ← wzór ustawień do pobrania z GitHub
├── config/
│   ├── app.php                        ← odczyt nazwy sklepu i języka z ustawień
│   ├── database.php                   ← konfiguracja połączenia z bazą
│   ├── mail.php                       ← konfiguracja wysyłki poczty
│   ├── session.php                    ← ustawienia sesji, w tym sesji koszyka
│   ├── auth.php                       ← ustawienia logowania i czasu ważności resetu hasła
│   └── filesystems.php                ← gdzie aplikacja przechowuje dodane pliki
├── database/
│   ├── migrations/                    ← instrukcje tworzenia i zmieniania tabel
│   │   ├── 0001_01_01_000000_create_users_table.php ← użytkownicy, sesje, reset hasła
│   │   ├── 2026_10_06_000001_create_shop_tables.php ← role i podstawowe tabele sklepu
│   │   └── pozostałe pliki .php        ← kolejne zmiany, np. zdjęcia i anulowanie
│   └── seeders/
│       ├── DatabaseSeeder.php         ← uruchamia dodawanie danych początkowych
│       └── ShopSeeder.php             ← konta DEMO, kategorie, przykładowe produkty
├── lang/
│   ├── pl/validation.php              ← polskie komunikaty błędów formularzy
│   └── pl.json                        ← dodatkowe polskie tłumaczenia
└── storage/
    ├── app/private/product-images/    ← zdjęcia dodane w panelu; poza GitHub
    └── logs/laravel.log               ← dziennik błędów aplikacji
```

Folder zdjęć i plik dziennika mogą powstać dopiero po pierwszym użyciu.
`pozostałe pliki .php` to opis grupy w drzewku, nie nazwa rzeczywistego pliku.

W lokalnym `.env` najczęściej szukasz:

| Co chcesz ustawić? | Jakiej nazwy szukać? |
|---|---|
| Nazwa sklepu | `APP_NAME` |
| Adres sklepu | `APP_URL` |
| Nazwa bazy danych | `DB_DATABASE` |
| Konto i hasło do bazy | `DB_USERNAME`, `DB_PASSWORD` |
| Serwer i port poczty | `MAIL_HOST`, `MAIL_PORT` |
| Gdzie zapisywana jest sesja | `SESSION_DRIVER` |

Po zmianie `.env`: **`php artisan config:clear`**.
`.env` zawiera sekrety — nie publikuj go i nie pokazuj haseł na nagraniu.
Same rekordy MariaDB nie są plikami w folderze `database`: oglądasz je w phpMyAdmin.
Migracje opisują strukturę, a seeder dostarcza dane przykładowe.

## 4. Instrukcje, testy i pliki techniczne

```text
bytegear/
├── MAPA_PLIKOW.md                      ← czytasz właśnie tę ściągę
├── README.md                          ← opis projektu i odnośniki do instrukcji
├── KOD_I_KONFIGURACJA_DO_OBRONY.md      ← czytelny poradnik: pliki i kod do obrony
├── POCZTA_SMTP_LUB_MAILPIT.txt          ← poczta, aktywacja i reset
├── ZDJECIA_PRODUKTOW.txt               ← grafiki, własne zdjęcia i limity
├── output/pdf/
│   ├── Instalacja_od_zera_CMD_ByteGear.pdf ← jeden przewodnik instalacji
│   └── Prezentacja_krok_po_kroku_ByteGear.pdf ← jeden scenariusz pokazu
├── docs/
│   ├── AKTUALIZACJA_I_KOPIA.md         ← aktualizacja, własne dane, nazwa sklepu
│   ├── WYMAGANIA.md                   ← mapa wymagań zaliczenia
│   └── pozostałe dokumenty .md         ← opis wyglądu i decyzji projektowych
├── tools/
│   ├── Start-Mailpit.ps1               ← uruchomienie lokalnej skrzynki e-mail
│   ├── Optimize-Demo-Images.php        ← przygotowanie lżejszych grafik WebP
│   ├── Generate-Beginner-Guide.py      ← tworzenie PDF-a krok po kroku
│   └── Generate-Cmd-Setup-Guide.py     ← tworzenie PDF-a instalacji od zera w CMD
├── tests/Feature/
│   ├── ShopTest.php                   ← testy podstawowych funkcji sklepu
│   ├── AccountSecurityTest.php        ← testy aktywacji, resetu i sesji
│   └── ShopExtensionsTest.php         ← testy profilu, zdjęć, anulowania, wiadomości
├── phpunit.xml                        ← ustawienia testów; osobna baza w pamięci
├── composer.json                      ← wymagane PHP i biblioteki projektu
├── composer.lock                      ← konkretne wersje bibliotek do odtworzenia instalacji
├── artisan                            ← narzędzie poleceń, np. uruchomienie serwera
└── vendor/                            ← biblioteki pobrane przez Composer; nie edytuj ich
```

## 5. Szybkie szukanie — kliknij odpowiedni plik

| Chcę znaleźć… | Otwieram… | Czego szukam przez Ctrl+F? |
|---|---|---|
| HTML strony głównej | [catalog/index.blade.php](resources/views/catalog/index.blade.php) | `Małe zmiany.` |
| Menu, nazwę i stopkę | [layouts/app.blade.php](resources/views/layouts/app.blade.php) | `<header`, `<footer`, `config('app.name')` |
| Kolory i czcionki | [shop.css](public/css/shop.css) | `background`, `font-family` |
| Wielkość głównego napisu | [shop.css](public/css/shop.css) | `.hero h1` |
| HTML logowania | [login.blade.php](resources/views/auth/login.blade.php) | `<form` |
| Sprawdzenie loginu i hasła | [AuthController.php](app/Http/Controllers/AuthController.php) | `function login`, `Auth::attempt` |
| HTML rejestracji | [register.blade.php](resources/views/auth/register.blade.php) | `<form` |
| Zabezpieczenie hasła w bazie | [User.php](app/Models/User.php) | `'password' => 'hashed'` |
| Dane kont demonstracyjnych | [ShopSeeder.php](database/seeders/ShopSeeder.php) | `@example.test`, `DemoSklep123!` — adres powstaje z roli, np. admin, i końcówki @example.test |
| Kontrolę roli | [RequireRole.php](app/Http/Middleware/RequireRole.php) | `403` |
| Różne strony po zalogowaniu | [OrderController.php](app/Http/Controllers/OrderController.php) | `function dashboard` |
| Listę użytkowników — HTML | [admin/users/index.blade.php](resources/views/admin/users/index.blade.php) | `Użytkownicy` |
| Dodawanie/usuwanie kont — działanie | [AdminUserController.php](app/Http/Controllers/AdminUserController.php) | `function store`, `function destroy` |
| Pola dodawania produktu | [admin/products/form.blade.php](resources/views/admin/products/form.blade.php) | `Dodaj produkt` |
| Wycofanie produktu ze sklepu | [AdminProductController.php](app/Http/Controllers/AdminProductController.php) | `function destroy` |
| HTML koszyka | [cart.blade.php](resources/views/cart.blade.php) | `@section` |
| Zapis koszyka w sesji | [CartController.php](app/Http/Controllers/CartController.php) | `put('cart'` |
| HTML adresu dostawy | [checkout.blade.php](resources/views/checkout.blade.php) | `Dokończ zamówienie` |
| Zapis i anulowanie zakupu | [OrderController.php](app/Http/Controllers/OrderController.php) | `function store`, `function cancel` |
| HTML szczegółów zakupu | [orders/show.blade.php](resources/views/orders/show.blade.php) | `Zamówienie #` |
| HTML swojego profilu | [profile.blade.php](resources/views/auth/profile.blade.php) | `<form` |
| Regex kodu pocztowego | [OrderController.php](app/Http/Controllers/OrderController.php) | `regex:` |
| Polskie błędy formularzy | [validation.php](lang/pl/validation.php) | `required`, `regex` |
| Spis adresów stron | [web.php](routes/web.php) | `Route::get` |
| Pobieranie kursu EUR z API | [CatalogController.php](app/Http/Controllers/CatalogController.php) | `function euro` |

**Najprostsza zasada:** zmiana tego, **co widać** → `resources/views`;
zmiana tego, **jak wygląda** → `public/css/shop.css`;
zmiana tego, **co program robi** → `app/Http/Controllers`.
