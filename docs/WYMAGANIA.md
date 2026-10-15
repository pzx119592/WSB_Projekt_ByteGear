# Wymagania i miejsca w kodzie

Scenariusz pokazania tych punktów w przeglądarce:
[Prezentacja krok po kroku — PDF](../output/pdf/Prezentacja_krok_po_kroku_ByteGear.pdf).
Szczegółowe metody i wyjaśnienia:
[KOD_I_KONFIGURACJA_DO_OBRONY.txt](../KOD_I_KONFIGURACJA_DO_OBRONY.txt).

| Wymaganie | Realizacja |
|---|---|
| Rejestracja i logowanie z bazą | AuthController, User, tabela users |
| Hashowanie haseł przy rejestracji i dodawaniu przez admina | cast password => hashed w User |
| Co najmniej dwa poziomy uprawnień | customer, moderator, admin; RequireRole i grupy tras |
| Różne strony po zalogowaniu | OrderController::dashboard |
| Dodawanie/usuwanie/modyfikacja użytkowników | AdminUserController; tylko administrator |
| Lista i pojedynczy użytkownik | /panel/users, /panel/users/{id} |
| Walidacja formularzy | validate() w kontrolerach; polskie komunikaty w lang/pl |
| Koszyk w sesji | CartController, Services/Cart, session cart |
| Dodawanie i usuwanie produktów przez moderatora/admina | AdminProductController; wycofanie ustawia active=false |
| Zamawianie przez klienta | OrderController, Order, OrderItem, transakcja i blokada produktów |
| Wyrażenia regularne | Hasło, kod pocztowy i SKU |
| Ochrona przed SQL Injection | Eloquent z parametrami, lista dozwolonych pól sortowania |
| API | Orientacyjna cena EUR z API NBP |
| Aktywacja przez e-mail | MustVerifyEmail, VerificationController, signed i verified middleware; lokalny SMTP Mailpit |
| Reset zapomnianego hasła | PasswordController, broker Laravel, jednorazowy token 60 minut, unieważnienie sesji |
| Edycja profilu i hasła | ProfileController; zmiana e-maila wymaga hasła i ponownej aktywacji |
| Własne zdjęcia produktów | AdminProductController + ProductImage; ograniczenia pliku, zapis bez EXIF |
| Anulowanie nowego zamówienia | OrderController::cancel; właściciel, blokada rekordu, jednorazowy zwrot zapasu |
| Potwierdzenie zamówienia e-mailem | OrderMail + OrderConfirmation; awaria SMTP nie cofa zakupu |

Usuwanie produktów oznacza ich wycofanie z katalogu, aby zachować historię zamówień.
Zamówienia są demonstracyjne; nie korzystają z bramki płatniczej ani zewnętrznego dostawcy.
Kategorie są dostarczone przez seeder, a przy edycji produktu można wybrać kategorię.

## Wiedza z ćwiczeń

Routing i parametry adresów, MVC, Blade i lokalny CSS, formularze POST i CSRF,
walidacja i regex, MariaDB i migracje, Artisan i seedery, Eloquent i relacje,
rejestracja/logowanie oraz pobieranie danych z API. Dodatkowo sesje koszyka,
rola moderatora/admina, transakcje, blokady i historyczne ceny pozycji.

## Weryfikacja

Testy automatyczne w tests/Feature/ShopTest.php obejmują przypadki poprawne,
odmowę dostępu, manipulację ceną, błędne dane, braki magazynowe, powtórne wysłanie
zamówienia, XSS, próbę SQL Injection i brak sieci dla NBP.
Zakup demonstracyjny sprawdzono również w przeglądarce na lokalnej MariaDB.
Nie jest to aplikacja przygotowana do obsługi rzeczywistych płatności produkcyjnych.

## Warunki pokazu i oddania

Mailpit odbiera aktywację przez SMTP lokalnie; do dostarczenia na zewnętrzną skrzynkę
potrzebny jest własny SMTP. Konfiguracja: [instrukcja poczty](../POCZTA_SMTP_LUB_MAILPIT.txt).
API NBP jest dodatkiem, a nie warunkiem złożenia zamówienia w PLN.
Samodzielność, termin oddania i obrona nie są potwierdzane przez testy aplikacji.
Projekt powstawał z pomocą asystenta; przedstaw swój wkład zgodnie z zasadami prowadzącego
i przećwicz wyjaśnienie kodu. Ocenę przyznaje prowadzący po prezentacji.
