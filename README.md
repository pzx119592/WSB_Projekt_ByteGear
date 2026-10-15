# Sklep z peryferiami komputerowymi — projekt zaliczeniowy

Aktualna nazwa: **ByteGear**. Laravel 12, PHP 8.2+, Blade, HTML5, CSS3 i MariaDB.
Osobna aplikacja, repozytorium i baza; wcześniejsze ćwiczenia pozostają w WSB_ZastProg.

**Instalacja od zera w CMD:** [PDF instalacji](output/pdf/Instalacja_od_zera_CMD_ByteGear.pdf).
**Co pokazać nauczycielowi:** [PDF prezentacji krok po kroku](output/pdf/Prezentacja_krok_po_kroku_ByteGear.pdf).
**Wymagania i powiązanie z kodem:** [docs/WYMAGANIA.md](docs/WYMAGANIA.md).
**Pliki, metody i konfiguracja do obrony:** [KOD_I_KONFIGURACJA_DO_OBRONY.txt](KOD_I_KONFIGURACJA_DO_OBRONY.txt).
**Poczta rzeczywista i opcja zapasowa:** [POCZTA_SMTP_LUB_MAILPIT.txt](POCZTA_SMTP_LUB_MAILPIT.txt).
**Aktualizacja, kopia danych i zmiana nazwy:** [docs/AKTUALIZACJA_I_KOPIA.md](docs/AKTUALIZACJA_I_KOPIA.md).
**Zdjęcia i ich przenoszenie:** [ZDJECIA_PRODUKTOW.txt](ZDJECIA_PRODUKTOW.txt).
**Przegląd wyglądu:** [docs/PRZEGLAD_WYGLADU.md](docs/PRZEGLAD_WYGLADU.md).
**Drzewko folderów i szybkie szukanie HTML, CSS oraz działania:** [MAPA_PLIKOW.md](MAPA_PLIKOW.md).

## Instrukcje PDF do pobrania

- **[Instalacja od zera w CMD — zacznij tutaj na nowym komputerze](output/pdf/Instalacja_od_zera_CMD_ByteGear.pdf)** — 4 strony, prosta lista 27 kroków: gdzie kliknąć i co wpisać. XAMPP, GD/ZIP, Composer i Laravel, import SQL lub dane przykładowe, .env, Mailpit oraz konta. Bez kolorowych ramek i ozdobników. Zastępuje poprzednią instrukcję instalacji w CMD.
- [Prezentacja krok po kroku dla początkujących](output/pdf/Prezentacja_krok_po_kroku_ByteGear.pdf) — 12 stron: co kliknąć, pełne ścieżki plików HTML i CSS, jak znaleźć je w VS Code oraz proste wyjaśnienia do prezentacji.

PDF-y zawierają adresy lokalnego sklepu i Mailpit. Linki do kodu otwierają GitHub;
w VS Code można znaleźć wskazane pliki przez Ctrl+P, a metody przez Ctrl+F.
Pozostawiono dwa aktualne PDF-y: instalację i prezentację. Szczegóły kodu są w TXT wskazanym wyżej.
PDF prezentacji tworzy `tools/Generate-Beginner-Guide.py` (ReportLab; niezależne od uruchomienia sklepu).
Instrukcję instalacji w CMD tworzy `tools/Generate-Cmd-Setup-Guide.py`.

## Kolejne uruchomienie już zainstalowanego sklepu

Włącz MySQL w XAMPP. Otwórz CMD i wykonaj polecenia pojedynczo:

```cmd
cd /d C:\xampp\htdocs\bytegear
set "PATH=C:\xampp\php;%PATH%"
powershell.exe -NoProfile -ExecutionPolicy Bypass -File tools\Start-Mailpit.ps1
php artisan serve --host=127.0.0.1 --port=8002
```

Zostaw terminal otwarty; Ctrl+C zatrzymuje serwer. Apache jest dodatkowo potrzebny do phpMyAdmin.
Na nowym komputerze wykonaj najpierw PDF instalacji. Przy zwykłym starcie nie kopiuj `.env`,
nie generuj ponownie klucza i nie resetuj bazy. Nie potrzebujesz Node.js ani npm.

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

## Aktywacja i odzyskiwanie hasła

Nowe konta wymagają potwierdzenia adresu przed korzystaniem z panelu i składaniem zamówień.
Link aktywacyjny ma podpis i ważność 60 minut. Można ponowić wysyłkę.
Zmiana adresu przez administratora wymaga ponownej aktywacji.
Trzy konta demonstracyjne z seedera są już aktywne.

Na stronie logowania jest „Nie pamiętam hasła”. Link resetu działa przez 60 minut,
tylko raz; zmiana hasła unieważnia wcześniejsze sesje. Odpowiedź nie ujawnia,
czy wskazany adres znajduje się w bazie.

Lokalne wiadomości odbiera [Mailpit](https://mailpit.axllent.org/) — **http://127.0.0.1:8025**.
Uruchom `powershell -NoProfile -ExecutionPolicy Bypass -File tools\Start-Mailpit.ps1`.
Skrypt pobiera oficjalny Mailpit 1.31.4 dla Windows x64 i sprawdza SHA256.
Zostaje zainstalowany poza repozytorium w `C:\xampp\tools\mailpit`.
Skrzynka jest dostępna tylko lokalnie. Wiadomości nie trafiają do rzeczywistych skrzynek.
Do wysyłki przez Internet można później podstawić własny serwer SMTP w `.env`.
Konfiguracja, aktywacja, reset i potwierdzenia: [POCZTA_SMTP_LUB_MAILPIT.txt](POCZTA_SMTP_LUB_MAILPIT.txt).

## Testy i inspiracje

`php artisan test` — osobna baza SQLite w pamięci; nie zmienia MariaDB.
Testy obejmują uprawnienia, CRUD, sesję koszyka, zamówienia, zapasy, próby manipulowania ceną,
walidację, hashowanie, CSRF, ucieczkę HTML, ochronę historii i brak dostępu do NBP.

Kategorie i typowe funkcje katalogu inspirowane [x-kom](https://www.x-kom.pl/) i
[Morele](https://www.morele.net/komputery/klawiatury-i-myszki/). Katalog zawiera własne
modele demonstracyjne BG i opisy dopasowane do naszych wizualizacji.
Opisy są własne; ceny i zapasy są przykładami edukacyjnymi. Osiemnaście fotorealistycznych
wizualizacji AI jest zapisanych lokalnie w public/images/products.
Są ilustracjami demonstracyjnymi, nie zdjęciami konkretnych modeli producentów.
Nie pobierano zdjęć z cudzych sklepów.
[Dokumentacja NBP](https://api.nbp.pl/), [Laravel 12](https://laravel.com/docs/12.x).
