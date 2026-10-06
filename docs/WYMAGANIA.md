# Wymagania i miejsca w kodzie

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
