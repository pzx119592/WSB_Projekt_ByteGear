# Sklep z peryferiami komputerowymi — projekt zaliczeniowy

Aktualna nazwa: **ByteGear**. Laravel 12, PHP 8.2+, Blade, HTML5, CSS3 i MariaDB.
Osobna aplikacja, repozytorium i baza; wcześniejsze ćwiczenia pozostają w WSB_ZastProg.

**Uruchomienie na drugim komputerze:** [INSTRUKCJA.txt](INSTRUKCJA.txt).
**Co pokazać nauczycielowi:** [PREZENTACJA.txt](PREZENTACJA.txt).
**Wymagania i powiązanie z kodem:** [docs/WYMAGANIA.md](docs/WYMAGANIA.md).

## Uruchomienie

Po przygotowaniu środowiska i `.env`:

```powershell
cd C:\xampp\htdocs\bytegear
$env:Path = "C:\xampp\php;" + $env:Path
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8002
```

W XAMPP musi działać MySQL. Utwórz najpierw bazę `bytegear` i skopiuj `.env.example` do `.env`.
Klucz generuj tylko przy nowej instalacji. Nie potrzebujesz Node.js ani npm.

Sklep: **http://127.0.0.1:8002/**. Port 8002 pozwala jednocześnie uruchomić ćwiczenia na 8000.

## Konta demonstracyjne

| Rola | E-mail | Hasło |
|---|---|---|
| Administrator | admin@example.test | DemoSklep123! |
| Moderator | moderator@example.test | DemoSklep123! |
| Klient | customer@example.test | DemoSklep123! |

Konta są przeznaczone do lokalnej prezentacji. Sklep, ceny i zamówienia mają charakter demonstracyjny;
nie ma integracji z rzeczywistą płatnością. Seeder tworzy 18 produktów i 6 kategorii.
Ponowne uruchomienie seedera nie resetuje istniejących kont ani stanów magazynowych.

## Zmiana nazwy sklepu

W lokalnym `.env` zmień tylko:

```dotenv
APP_NAME="Moja nowa nazwa"
```

Następnie `php artisan config:clear` i odśwież przeglądarkę.
Nagłówek, stopka i tytuły stron korzystają z `config('app.name')`. Ilustracje nie mają wpisanej nazwy.
Dla przyszłych pobrań zmień także `APP_NAME` w `.env.example` i wyślij zmianę do repozytorium.
Nazwa katalogu, bazy i repozytorium może pozostać taka sama — nie określa marki sklepu.
Po zmianie nazwy może być potrzebne ponowne logowanie, ponieważ zmienia się nazwa ciasteczka sesji.

## Zakres wersji podstawowej

Katalog z filtrowaniem, sortowaniem i paginacją; szczegóły produktu; koszyk w sesji;
rejestracja i logowanie; różne ekrany dla trzech ról; CRUD użytkowników dla administratora;
zarządzanie produktami dla moderatora i administratora; zamówienia i ich statusy.

Ceny są zapisane w groszach. Złożenie zamówienia sprawdza dostępność w transakcji,
blokuje produkty i zapisuje historyczne nazwy oraz ceny. Wycofanie produktu usuwa go z oferty,
a historia zamówień pozostaje. Usunięcie użytkownika nie usuwa zamówień.

Dodatkowo: polska walidacja, regex dla SKU, kodu pocztowego i hasła, CSRF,
uprawnienia na serwerze, zapytania parametryzowane i prezentacja ceny EUR z API NBP.
API nie jest potrzebne do złożenia zamówienia w PLN. Obrazy i CSS są lokalne.

**Aktywacja konta e-mailem jest zaplanowana na późniejszy etap, zgodnie z ustaleniem.**

## Testy i inspiracje

`php artisan test` — osobna baza SQLite w pamięci; nie zmienia MariaDB.
Testy obejmują uprawnienia, CRUD, sesję koszyka, zamówienia, zapasy, próby manipulowania ceną,
walidację, hashowanie, CSRF, ucieczkę HTML, ochronę historii i brak dostępu do NBP.

Kategorie i typowe funkcje katalogu inspirowane [x-kom](https://www.x-kom.pl/) i
[Morele](https://www.morele.net/komputery/klawiatury-i-myszki/). Własne nazwy produktów,
opisy i ilustracje SVG; ceny i parametry są przykładami edukacyjnymi.
[Dokumentacja NBP](https://api.nbp.pl/), [Laravel 12](https://laravel.com/docs/12.x).
