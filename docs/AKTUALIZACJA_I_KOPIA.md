# Aktualizacja, kopia własnych danych i zmiana nazwy

Pierwszą instalację opisuje [PDF instalacji w CMD](../output/pdf/Instalacja_od_zera_CMD_ByteGear.pdf).
Poniższe kroki dotyczą istniejącej instalacji. Polecenia wykonuj w **CMD**, pojedynczo.

## Aktualizacja istniejącej instalacji

1. Zrób prywatną kopię bazy, `.env` i własnych zdjęć według następnej sekcji.
2. Zatrzymaj sklep przez Ctrl+C w jego terminalu, pozostaw MySQL włączony.
3. Przy pobraniu przez Git wykonaj `git pull`; własne zmiany najpierw zachowaj.
   Przy pobraniu ZIP podmień źródła, zachowując lokalne `.env` oraz `storage`.
4. W nowym CMD wykonaj kolejno:

```cmd
cd /d C:\xampp\htdocs\bytegear
set "PATH=C:\xampp\php;%PATH%"
composer install
composer check-platform-reqs
php artisan migrate
php artisan config:clear
php artisan view:clear
```

5. Uruchom Mailpit i serwer według [README](../README.md).

Nie nadpisuj `.env`, nie generuj ponownie `APP_KEY` i nie wykonuj `migrate:fresh`.
Migracje mogą aktualizować nazwy katalogu demonstracyjnego; nie zmieniają historycznych
nazw ani cen w pozycjach zamówień. `db:seed` nie jest obowiązkowym krokiem aktualizacji.

## Kopia dokładnie swoich kont, zamówień i zdjęć

GitHub przechowuje kod, migracje i dane przykładowe. Nie przechowuje bieżących rekordów
Twojej MariaDB, lokalnego `.env` ani zdjęć przesłanych przez panel.

1. Na czas kopii nie dodawaj zamówień ani nie edytuj danych.
2. W phpMyAdmin wybierz bazę `bytegear` → Eksport → Szybki → SQL → Eksportuj.
   Zachowaj strukturę i dane.
3. Skopiuj folder `storage/app/private/product-images`, zachowując nazwy plików.
   Jeśli nie przesyłano zdjęć, folder może nie istnieć.
4. Zachowaj prywatną kopię `.env`, w tym istniejący `APP_KEY`.

Na drugim komputerze:

1. Przygotuj środowisko i pobierz projekt według PDF-a instalacji.
2. Importuj SQL do **pustej** bazy w phpMyAdmin. Nie kasuj innej używanej bazy.
3. Skopiuj zdjęcia do `storage/app/private/product-images` w nowym projekcie.
4. Przy pełnym przeniesieniu ustaw `.env` z zachowaniem dotychczasowego `APP_KEY`,
   dostosowując `DB_*`, `APP_URL` i `MAIL_*` do nowego komputera.
5. Wykonaj `composer install`, `php artisan migrate` i `php artisan config:clear`.
6. Uruchom sklep i zaloguj się ponownie. Obowiązują hasła z importowanej bazy.

SQL i `.env` przenoś prywatnie, np. pendrivem. Nie dodawaj ich do publicznego repozytorium.
Jeśli wystarczą dane demonstracyjne, na pustej bazie użyj `php artisan migrate --seed`
zgodnie z PDF-em. Domowa kopia nie jest wtedy potrzebna.

## Zmiana nazwy sklepu

W lokalnym `.env` zmień:

```dotenv
APP_NAME="Moja nowa nazwa"
```

Następnie `php artisan config:clear` i odśwież stronę.
Nagłówek, stopka, tytuły i wiadomości używają `config('app.name')`.
Może być potrzebne ponowne logowanie, ponieważ nazwa wpływa na nazwę ciasteczka sesji.
Dla przyszłych pobrań zmień też `APP_NAME` w `.env.example`.
Nie musisz zmieniać nazwy folderu, bazy ani repozytorium. BG jest osobną serią modeli demo.
