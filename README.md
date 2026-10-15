# ByteGear — instalacja i uruchomienie sklepu

Instrukcja na Windows, od początku. **Polecenia wpisuj w CMD, każde osobno, i naciskaj Enter.**
Jeśli pojawi się błąd, zatrzymaj się na tym kroku.

[Ta instrukcja w PDF](output/pdf/Instalacja_od_zera_CMD_ByteGear.pdf) ·
[Prezentacja dla nauczyciela](output/pdf/Prezentacja_krok_po_kroku_ByteGear.pdf) ·
[Drzewko plików](MAPA_PLIKOW.md)

Jeśli sklep jest już zainstalowany, przejdź do [kolejnego uruchomienia](#kolejne-uruchomienie).

## 1. Zainstaluj i włącz XAMPP

Pobierz [XAMPP dla Windows z PHP 8.2](https://www.apachefriends.org/download.html).
Jeśli jest już zainstalowany, pomiń instalację.

1. Uruchom instalator. Potrzebne składniki: Apache, PHP, MySQL/MariaDB i phpMyAdmin.
2. Zainstaluj w `C:\xampp`.
3. Otwórz **XAMPP Control Panel**.
4. Kliknij **Start** przy **Apache** i **MySQL**. Obie pozycje powinny być zielone.

## 2. Włącz GD i ZIP w PHP

1. W Notatniku otwórz **`C:\xampp\php\php.ini`**.
2. Naciśnij Ctrl+F i znajdź `extension=gd`.
3. Usuń początkowy średnik `;`, jeśli jest. Tak samo zrób z `extension=zip`.

Linie mają wyglądać tak:

```ini
extension=gd
extension=zip
```

Nie dopisuj drugiej aktywnej kopii tych samych linii. Zapisz **Ctrl+S** i zamknij Notatnik.
W XAMPP kliknij **Stop**, a następnie **Start** przy Apache.

## 3. Zainstaluj Composer i otwórz CMD

1. Pobierz i uruchom [Composer-Setup.exe](https://getcomposer.org/Composer-Setup.exe).
2. Gdy instalator pyta o PHP, wskaż **`C:\xampp\php\php.exe`**.
3. Proxy zostaw niewłączone, chyba że uczelnia podała własne ustawienia.
4. Dokończ instalację i zamknij stare terminale.
5. Naciśnij **Windows+R**, wpisz **`cmd`** i Enter.

To ma być **Wiersz polecenia**, nie PowerShell zaczynający się od `PS`.
W CMD wklej to polecenie i naciśnij Enter:

```cmd
set "PATH=C:\xampp\php;%PATH%"
```

Następnie osobno sprawdź PHP:

```cmd
php --version
```

I Composer:

```cmd
composer --version
```

Powinny wyświetlić się wersje obu programów. Jeśli polecenie nie jest rozpoznawane,
sprawdź instalację i otwórz nowe CMD przed dalszymi krokami.

## 4. Pobierz i wypakuj sklep

1. Na górze tego repozytorium kliknij **Code → Download ZIP**.
2. W Eksploratorze plików wypakuj ZIP przez **Wyodrębnij wszystkie**.
3. Folder aplikacji przenieś do `C:\xampp\htdocs` i nazwij **`bytegear`**.
4. Sprawdź, czy plik `artisan` znajduje się dokładnie tutaj:

```text
C:\xampp\htdocs\bytegear\artisan
```

Pliki `composer.json` i `.env.example` też muszą być bezpośrednio w `bytegear`.
Jeśli jest dodatkowy folder `WSB_Projekt_ByteGear-main`, przenieś jego zawartość o poziom wyżej.

## 5. Zainstaluj Laravel i biblioteki projektu

Wróć do CMD. Wejdź do projektu:

```cmd
cd /d C:\xampp\htdocs\bytegear
```

Utwórz plik ustawień. **Jeśli masz już własny `.env`, pomiń kopiowanie i go nie nadpisuj.**

```cmd
copy .env.example .env
```

Zainstaluj biblioteki i poczekaj na zakończenie:

```cmd
composer install
```

To instaluje również Laravel naszego sklepu. Nie trzeba `laravel new`, Node.js ani npm.
Prawidłowa pisownia to **`install`**, nie `isntall`.

Sprawdź wymagania:

```cmd
composer check-platform-reqs
```

Sprawdź Laravel:

```cmd
php artisan --version
```

Oczekiwany wynik: **Laravel Framework 12.x.x**.

- **`ext-gd is missing`**: wróć do kroku 2, włącz GD, zapisz i ponów `composer install`.
- **Brak `vendor/autoload.php`**: `composer install` nie zakończył się poprawnie. Najpierw napraw jego błąd.
- Nie wykonuj `composer update` ani pomijania wymagań, żeby obejść te błędy.

## 6. Utwórz bazę i ewentualnie importuj SQL

W przeglądarce otwórz **[phpMyAdmin](http://localhost/phpmyadmin/)**.

1. Kliknij **Nowa**.
2. Wpisz nazwę **`bytegear`**.
3. Wybierz porównywanie **`utf8mb4_unicode_ci`**.
4. Kliknij **Utwórz**.

Jeśli baza `bytegear` już istnieje i zawiera dane, nie kasuj jej.
Do nowej instalacji utwórz np. `bytegear_pokaz` i wpisz tę nazwę w kolejnym kroku.

**Masz domową kopię `.sql`?** Wybierz pustą bazę po lewej → **Import → Wybierz plik** →
wskaż `.sql` → **Importuj/Wykonaj**. Poczekaj na komunikat sukcesu.

**Nie masz SQL?** Zostaw pustą bazę i idź dalej. W kroku 9 utworzysz dane przykładowe.

## 7. Ustaw połączenie z bazą

W CMD otwórz plik ustawień:

```cmd
notepad .env
```

**Teraz pracujesz w Notatniku, nie w CMD.** Znajdź poniższe linie i popraw ich wartości.
Resztę pliku zostaw; nie dodawaj wielokrotnie tych samych ustawień.

```dotenv
APP_URL=http://127.0.0.1:8002
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bytegear
DB_USERNAME=root
DB_PASSWORD=
SESSION_DRIVER=database
CACHE_STORE=database
```

`DB_DATABASE` musi pasować do nazwy bazy z kroku 6. Puste `DB_PASSWORD=` pasuje
do standardowego XAMPP. Jeśli uczelnia ma inne konto, hasło lub port, użyj właściwych danych.

## 8. Ustaw pocztę lokalną

**W tym samym Notatniku**, w `.env`, sprawdź:

```dotenv
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="sklep@bytegear.test"
MAIL_FROM_NAME="${APP_NAME}"
```

Zapisz **Ctrl+S** i zamknij Notatnik. Plik ma nazywać się `.env`, nie `.env.txt`.

## 9. Przygotuj klucz i tabele

Wróć do CMD. Na nowej instalacji wygeneruj klucz:

```cmd
php artisan key:generate
```

Jeśli przeniosłeś domowy `.env` z istniejącym `APP_KEY`, pomiń to polecenie i zachowaj jego klucz.

Wczytaj ustawienia:

```cmd
php artisan config:clear
```

**Wybierz tylko jeden wariant dla bazy:**

**A. Zaimportowałeś domowy SQL:**

```cmd
php artisan migrate
```

**B. Masz pustą bazę i chcesz przykładowe konta oraz produkty:**

```cmd
php artisan migrate --seed
```

Nie używaj `migrate:fresh` — usuwa istniejące tabele i dane.
Przy błędzie połączenia sprawdź MySQL w XAMPP oraz ustawienia z kroku 7.

## 10. Uruchom pocztę i sklep

W CMD uruchom pocztę:

```cmd
powershell.exe -NoProfile -ExecutionPolicy Bypass -File tools\Start-Mailpit.ps1
```

To polecenie uruchamia gotowy skrypt z poziomu CMD. Przy pierwszym starcie pobiera Mailpit z Internetu.

Następnie uruchom sklep:

```cmd
php artisan serve --host=127.0.0.1 --port=8002
```

**Zostaw to okno CMD otwarte.** Ctrl+C zatrzymuje sklep.

## 11. Otwórz stronę i zaloguj się

- **[Sklep](http://127.0.0.1:8002/)**
- **[Logowanie](http://127.0.0.1:8002/logowanie)**
- **[Poczta Mailpit](http://127.0.0.1:8025/)** — aktywacje kont, reset hasła i potwierdzenia zamówień

| Rola | E-mail | Hasło |
|---|---|---|
| Klient | customer@example.test | DemoSklep123! |
| Moderator | moderator@example.test | DemoSklep123! |
| Administrator | admin@example.test | DemoSklep123! |

Konta demo są aktywowane. Po imporcie domowego SQL obowiązują hasła zapisane w tej kopii,
także te zmienione ręcznie. Nowe konto potwierdzasz przez link w Mailpit.
Wiadomości z Mailpit nie trafiają do Gmaila ani innych zewnętrznych skrzynek.

## Kolejne uruchomienie

Włącz **MySQL** w XAMPP; Apache także, jeśli chcesz phpMyAdmin.
Otwórz nowe CMD i wykonaj **każde polecenie osobno**:

```cmd
cd /d C:\xampp\htdocs\bytegear
```

```cmd
set "PATH=C:\xampp\php;%PATH%"
```

```cmd
powershell.exe -NoProfile -ExecutionPolicy Bypass -File tools\Start-Mailpit.ps1
```

```cmd
php artisan serve --host=127.0.0.1 --port=8002
```

Otwórz [sklep](http://127.0.0.1:8002/). Nie kopiuj ponownie `.env`, nie generuj nowego klucza
i nie importuj ponownie bazy przy zwykłym starcie.

## Pozostałe instrukcje

- [Prezentacja dla nauczyciela — PDF](output/pdf/Prezentacja_krok_po_kroku_ByteGear.pdf)
- [Drzewko plików HTML, CSS i działania sklepu](MAPA_PLIKOW.md)
- [Dokładne miejsca w kodzie do obrony](KOD_I_KONFIGURACJA_DO_OBRONY.txt)
- [Aktualizacja, kopia bazy i własnych zdjęć, zmiana nazwy](docs/AKTUALIZACJA_I_KOPIA.md)
- [Poczta lokalna i prawdziwy SMTP](POCZTA_SMTP_LUB_MAILPIT.txt)
- [Zdjęcia produktów](ZDJECIA_PRODUKTOW.txt)
- [Wymagania zaliczenia](docs/WYMAGANIA.md)

Projekt zaliczeniowy, Laravel 12 / PHP 8.2 / MariaDB. Dane i zakupy są demonstracyjne;
nie są pobierane prawdziwe płatności.
