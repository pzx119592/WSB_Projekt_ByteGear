"""Generate the two Polish handover guides. Requires ReportLab (not the shop).

Run using a Python environment with reportlab installed. No shop data is changed.
"""
from pathlib import Path
from xml.sax.saxutils import escape

from reportlab.lib import colors
from reportlab.lib.enums import TA_LEFT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, PageBreak, Table, TableStyle, KeepTogether,
)

ROOT = Path(__file__).resolve().parents[1]
OUT = ROOT / "output" / "pdf"
OUT.mkdir(parents=True, exist_ok=True)
for name, filename in [("Segoe", "segoeui.ttf"), ("SegoeBold", "segoeuib.ttf"), ("Code", "consola.ttf")]:
    pdfmetrics.registerFont(TTFont(name, str(Path("C:/Windows/Fonts") / filename)))
pdfmetrics.registerFontFamily("Segoe", normal="Segoe", bold="SegoeBold", italic="Segoe", boldItalic="SegoeBold")
BLUE = colors.HexColor("#2455DB")
INK = colors.HexColor("#172B46")
LIGHT = colors.HexColor("#EDF3FC")
MUTED = colors.HexColor("#50627A")
WIDTH = A4[0] - 88
styles = getSampleStyleSheet()
styles.add(ParagraphStyle("Body", fontName="Segoe", fontSize=10.3, leading=14.3, textColor=INK, spaceAfter=7))
styles.add(ParagraphStyle("TitlePL", fontName="SegoeBold", fontSize=24, leading=29, textColor=INK, spaceAfter=12))
styles.add(ParagraphStyle("SectionPL", fontName="SegoeBold", fontSize=16, leading=20, textColor=INK, spaceAfter=10))
styles.add(ParagraphStyle("SubPL", fontName="SegoeBold", fontSize=11.5, leading=16, textColor=BLUE, spaceBefore=5, spaceAfter=6))
styles.add(ParagraphStyle("SmallPL", fontName="Segoe", fontSize=9, leading=12.4, textColor=MUTED, spaceAfter=5))
styles.add(ParagraphStyle("CodePL", fontName="Code", fontSize=9, leading=12.5, textColor=INK, spaceAfter=0, splitLongWords=True))
styles.add(ParagraphStyle("TablePL", fontName="Segoe", fontSize=9.4, leading=13, textColor=INK))

def p(text, style="Body"):
    return Paragraph(text, styles[style])

def link(url, label=None):
    return f'<a href="{escape(url, {chr(34): "&quot;"})}" color="#2455DB"><u>{escape(label or url)}</u></a>'

def code(text):
    rows = [p(escape(line).replace(" ", "&#160;"), "CodePL") for line in text.splitlines()]
    t = Table([[rows]], colWidths=[WIDTH])
    t.setStyle(TableStyle([("BACKGROUND", (0, 0), (-1, -1), LIGHT), ("LEFTPADDING", (0,0),(-1,-1),10), ("RIGHTPADDING",(0,0),(-1,-1),10), ("TOPPADDING",(0,0),(-1,-1),9), ("BOTTOMPADDING",(0,0),(-1,-1),9)]))
    return [t, Spacer(1, 9)]

def table(rows, widths):
    cells = [[p(escape(str(c)), "TablePL") for c in row] for row in rows]
    t = Table(cells, colWidths=widths, repeatRows=1, hAlign="LEFT")
    t.setStyle(TableStyle([("BACKGROUND",(0,0),(-1,0),LIGHT), ("VALIGN",(0,0),(-1,-1),"TOP"), ("GRID",(0,0),(-1,-1),0.4,colors.HexColor("#CCD6E4")), ("LEFTPADDING",(0,0),(-1,-1),8), ("RIGHTPADDING",(0,0),(-1,-1),8), ("TOPPADDING",(0,0),(-1,-1),7), ("BOTTOMPADDING",(0,0),(-1,-1),7)]))
    return [t, Spacer(1, 10)]

def files(items):
    result = []
    for path, find in items:
        url = "https://github.com/pzx119592/WSB_Projekt_ByteGear/blob/main/" + path
        # Separate the path and the search hint so long controller names remain readable.
        result += [p(link(url, path), "SmallPL"), p("Znajdź: <b>" + escape(find) + "</b>", "SmallPL")]
    return result

def block(title, url, action, expected, paths, explain):
    head = [p(title, "SubPL"), p("<b>Przeglądarka:</b> " + link("http://127.0.0.1:8002" + url))]
    return head + [p("<b>Zrób:</b> " + action), p("<b>Wynik:</b> " + expected)] + files(paths) + [p("<b>Powiedz:</b> " + explain), Spacer(1, 8)]

def page(title, subtitle=None):
    out = [p(title, "SectionPL")]
    if subtitle:
        out += [p(subtitle, "SmallPL")]
    return out

def footer(canvas, doc):
    canvas.saveState()
    canvas.setStrokeColor(colors.HexColor("#D8E1EE"))
    canvas.line(44, 43, A4[0]-44, 43)
    canvas.setFont("Segoe", 8)
    canvas.setFillColor(MUTED)
    canvas.drawString(44, 29, "ByteGear | projekt zaliczeniowy | instrukcja z 13.10.2026")
    canvas.drawRightString(A4[0]-44, 29, str(doc.page))
    canvas.restoreState()

def build(filename, title, pages):
    story = []
    for i, content in enumerate(pages):
        if i:
            story.append(PageBreak())
        story.extend(content)
    doc = SimpleDocTemplate(str(OUT / filename), pagesize=A4, leftMargin=44, rightMargin=44, topMargin=42, bottomMargin=60, title=title, author="ByteGear - dokumentacja projektu", subject="Laravel 12 / PHP 8.2 / MariaDB")
    doc.build(story, onFirstPage=footer, onLaterPages=footer)

# -----------------------------------------------------------------------------
# PDF 1: installation, everyday startup, update and private backup.
# -----------------------------------------------------------------------------
run = []
run.append([
    p("Uruchomienie sklepu", "TitlePL"),
    p("ByteGear na swoim i na drugim komputerze", "SubPL"),
    p("Ten dokument obejmuje uruchomienie gotowej instalacji oraz pobranie projektu z GitHub na komputer uczelniany. Sklep działa lokalnie, bez rzeczywistych płatności."),
    p("1. Szybki start u siebie", "SubPL"),
    p("W XAMPP Control Panel włącz <b>MySQL</b> (to MariaDB). Apache jest potrzebny tylko do phpMyAdmin; sklep uruchamiamy serwerem Artisan. Otwórz PowerShell i wykonaj:"),
    *code('cd C:\\xampp\\htdocs\\bytegear\n$env:Path = "C:\\xampp\\php;" + $env:Path\npowershell -NoProfile -ExecutionPolicy Bypass -File tools\\Start-Mailpit.ps1\nphp artisan serve --host=127.0.0.1 --port=8002'),
    p("Zostaw terminal z Artisan otwarty. <b>Ctrl+C</b> zatrzymuje sklep. Mailpit działa w tle; skrypt uruchamia go lub wykorzystuje istniejący proces."),
    *table([
        ["Co otworzyć", "Adres"],
        ["Sklep", "http://127.0.0.1:8002/"],
        ["Lokalna skrzynka Mailpit", "http://127.0.0.1:8025/"],
        ["phpMyAdmin (wymaga Apache)", "http://localhost/phpmyadmin/"],
    ], [185, WIDTH-185]),
    p("Kliknij: " + link("http://127.0.0.1:8002/", "sklep") + " | " + link("http://127.0.0.1:8025/", "Mailpit") + " | " + link("http://localhost/phpmyadmin/", "phpMyAdmin")),
    p("Konta demonstracyjne", "SubPL"),
    *table([["Rola", "E-mail"], ["Administrator", "admin@example.test"], ["Moderator", "moderator@example.test"], ["Klient", "customer@example.test"]], [150, WIDTH-150]),
    p("Hasło każdego konta: <b>DemoSklep123!</b>. Konta są już aktywowane. Nowe konta wymagają kliknięcia wiadomości w Mailpit. Dane pochodzą z seedera; po ręcznej zmianie hasła użyj swojego nowego hasła."),
    p("Spis: s. 2 - nowy komputer; s. 3 - konfiguracja i pierwsze uruchomienie; s. 4 - aktualizacja, kopia danych i problemy.", "SmallPL"),
])
run.append(page("2. Pobranie na nowy komputer") + [
    p("Przygotuj środowisko", "SubPL"),
    p("Zainstaluj XAMPP z PHP 8.2 lub zgodnym nowszym PHP, MariaDB i phpMyAdmin. Obecnie projekt jest sprawdzony na <b>PHP 8.2.12 i Laravel 12</b>. Włącz MySQL. Zainstaluj Composer, wskazując <b>C:\\xampp\\php\\php.exe</b>. Visual Studio Code służy do pokazu kodu, Git jest opcjonalny."),
    p(link("https://www.apachefriends.org/", "XAMPP") + " | " + link("https://getcomposer.org/", "Composer") + " | " + link("https://code.visualstudio.com/", "Visual Studio Code")),
    p("W PHP musi być włączone rozszerzenie <b>GD</b> do zdjęć. Sprawdź C:\\xampp\\php\\php.ini: aktywna linia <b>extension=gd</b> nie może zaczynać się od średnika. Po zmianie uruchom ponownie serwer PHP; composer check-platform-reqs sprawdzi również pozostałe wymagania."),
    p("Pobierz całe repozytorium", "SubPL"),
    p(link("https://github.com/pzx119592/WSB_Projekt_ByteGear", "Otwórz repozytorium sklepu") + ". Kliknij <b>Code &gt; Download ZIP</b>, wypakuj i nazwij folder <b>bytegear</b>. Umieść go w C:\\xampp\\htdocs. Plik artisan ma być bezpośrednio w C:\\xampp\\htdocs\\bytegear, bez dodatkowego zagnieżdżenia."),
    p("Alternatywa z zainstalowanym Git (wybierz jedną metodę pobrania):"),
    *code('cd C:\\xampp\\htdocs\ngit clone https://github.com/pzx119592/WSB_Projekt_ByteGear.git bytegear'),
    p("Utwórz pustą bazę", "SubPL"),
    p("Włącz Apache i wejdź do " + link("http://localhost/phpmyadmin/") + ". Wybierz <b>Nowa</b>, nazwa <b>bytegear</b>, porównywanie <b>utf8mb4_unicode_ci</b>. Tabele utworzy Laravel. Jeśli baza o tej nazwie już istnieje i zawiera dane, zachowaj ją lub utwórz inną i wpisz jej nazwę w .env."),
    p("Zależności i plik konfiguracji", "SubPL"),
    *code('cd C:\\xampp\\htdocs\\bytegear\n$env:Path = "C:\\xampp\\php;" + $env:Path\nphp --version\ncomposer --version\nCopy-Item .env.example .env\ncomposer install\ncomposer check-platform-reqs'),
    p("Copy-Item wykonaj tylko wtedy, gdy nie masz własnego .env. <b>composer install</b> korzysta z composer.lock; nie zamieniaj go na composer update. Nie potrzebujesz Node.js, npm ani globalnego instalatora Laravel."),
    p("Przy pierwszej instalacji potrzebny jest Internet do zależności i pobrania Mailpit. Po ich pobraniu główne funkcje sklepu działają offline. Tylko odczyt kursu EUR z NBP wymaga Internetu.", "SmallPL"),
])
run.append(page("3. Konfiguracja i pierwszy start") + [
    p("Otwórz .env w folderze sklepu. Wpisz ustawienia swojej MariaDB. Poniżej wariant lokalnego XAMPP z pustym hasłem root; na uczelni użyj danych dostępnych na tamtym komputerze."),
    *code('APP_NAME=ByteGear\nAPP_URL=http://127.0.0.1:8002\nAPP_LOCALE=pl\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=bytegear\nDB_USERNAME=root\nDB_PASSWORD=\nSESSION_DRIVER=database\nCACHE_STORE=database'),
    p("Poczta lokalna - wariant do prezentacji", "SubPL"),
    *code('MAIL_MAILER=smtp\nMAIL_SCHEME=null\nMAIL_HOST=127.0.0.1\nMAIL_PORT=1025\nMAIL_USERNAME=null\nMAIL_PASSWORD=null\nMAIL_FROM_ADDRESS="sklep@bytegear.test"\nMAIL_FROM_NAME="${APP_NAME}"'),
    p("Mailpit odbiera wiadomości pod dowolnymi adresami, ale <b>nie wysyła ich do rzeczywistych skrzynek</b>. W przeglądarce otwierasz http://127.0.0.1:8025 i klikasz link w przechwyconej wiadomości. Prawdziwy SMTP można podstawić w .env; instrukcja jest w POCZTA_SMTP_LUB_MAILPIT.txt."),
    p("Wykonaj w terminalu (nowa instalacja)", "SubPL"),
    *code('php artisan key:generate\nphp artisan config:clear\nphp artisan migrate --seed\npowershell -NoProfile -ExecutionPolicy Bypass -File tools\\Start-Mailpit.ps1\nphp artisan serve --host=127.0.0.1 --port=8002'),
    p("Otwórz " + link("http://127.0.0.1:8002/") + ". Powinno być 18 demonstracyjnych produktów w 6 kategoriach i trzy konta ze strony 1. Sprawdź logowanie klienta, koszyk oraz skrzynkę Mailpit."),
    p("Klucz APP_KEY generuj <b>tylko raz dla nowej instalacji</b>. Nie uruchamiaj migrate:fresh na istniejącej bazie: usuwa dane. Do zwykłej prezentacji wystarczy migrate --seed; nie trzeba importować domowej bazy, uruchamiać kolejki ani wykonywać storage:link.", "SmallPL"),
])
run.append(page("4. Aktualizacja, kopia i problemy") + [
    p("Aktualizacja istniejącej instalacji", "SubPL"),
    p("Zachowaj swój .env, bazę i zdjęcia. Przy pobraniu przez Git wykonaj git pull w folderze projektu; przy ZIP podmień pliki źródłowe bez kasowania własnej konfiguracji i storage."),
    *code('composer install\nphp artisan migrate\nphp artisan config:clear\nphp artisan view:clear'),
    p("Nie generuj nowego APP_KEY i nie resetuj bazy. Jeśli potrzebujesz danych przykładowych, php artisan db:seed odtwarza dane demonstracyjne bez resetowania stanów magazynowych."),
    p("Przeniesienie dokładnie swoich danych", "SubPL"),
    p("GitHub zawiera kod, migracje, przykładowe produkty i grafiki. <b>Nie zawiera Twoich kont, zamówień, lokalnego .env ani zdjęć dodanych w panelu.</b> Dla identycznego stanu zatrzymaj zapisy w sklepie i wykonaj prywatną kopię:"),
    p("1. W phpMyAdmin wybierz bazę bytegear &gt; Eksport &gt; SQL (struktura i dane).<br/>2. Skopiuj storage/app/private/product-images z zachowaniem nazw plików.<br/>3. Na drugim komputerze importuj SQL do pustej bazy i skopiuj zdjęcia do tej samej ścieżki projektu.<br/>4. Ustaw .env pod tamten komputer. Przy pełnej kopii zachowaj pierwotny APP_KEY; zmień adres/połączenie z bazą. Wykonaj migrate i config:clear, a następnie zaloguj się ponownie."),
    p("SQL i .env przenoś prywatnie, np. pendrivem. Zawierają dane kont lub sekrety i nie powinny trafić do publicznego repozytorium. Świeża instalacja z seedera nie wymaga tej kopii."),
    p("Najczęstsze problemy", "SubPL"),
    *table([
        ["Objaw", "Co zrobić"],
        ["php / composer nie jest rozpoznany", "Ustaw PATH jak na s. 1; zamknij i otwórz terminal po instalacji Composera."],
        ["Błąd bazy / connection refused", "Włącz MySQL. Sprawdź DB_DATABASE, port i dane konta w .env; config:clear."],
        ["Brak GD / błąd zależności", "Włącz extension=gd w php.ini PHP używanego przez Composer. Sprawdź php --ini."],
        ["Port 8002 zajęty", "Sprawdź, czy sklep już działa. Dla innego portu zmień APP_URL i --port, potem config:clear."],
        ["Brak e-maila / link 403", "Uruchom Mailpit i wyślij link ponownie. Używaj 127.0.0.1 oraz właściwego konta; link aktywacji jest ważny 60 minut."],
    ], [165, WIDTH-165]),
    p("Do zdjęć do 4 MB ustaw odpowiednio upload_max_filesize (np. 8M) i post_max_size (np. 12M) w php.ini. Zmiany PHP wymagają ponownego uruchomienia serwera.", "SmallPL"),
])
build("Uruchomienie_sklepu_ByteGear.pdf", "Uruchomienie sklepu ByteGear", run)

# -----------------------------------------------------------------------------
# PDF 2: grading checklist and the browser-to-code demonstration.
# -----------------------------------------------------------------------------
show = []
show.append([
    p("Prezentacja projektu", "TitlePL"),
    p("Co pokazać w przeglądarce i gdzie znaleźć kod", "SubPL"),
    p("Przewodnik po wszystkich technicznych punktach zaliczenia oraz dodatkach sklepu ByteGear. Adresy stron i ścieżki do źródeł są klikalne. Link do kodu prowadzi do GitHub; na komputerze otwórz folder bytegear w Visual Studio Code."),
    p("Jak szybko znaleźć plik", "SubPL"),
    p("<b>Ctrl+P</b> - wklej ścieżkę z tego dokumentu i Enter. <b>Ctrl+F</b> - wyszukaj nazwę metody lub fragment podany jako „Znajdź”. Ścieżki są względem folderu projektu. Metody są pewniejsze niż numery linii, które zmieniają się po aktualizacji."),
    p("Przed pokazem", "SubPL"),
    p("Uruchom MariaDB, sklep na porcie 8002 i Mailpit na 8025 zgodnie z pierwszym PDF-em. Przygotuj VS Code i phpMyAdmin. Konta admin@example.test, moderator@example.test i customer@example.test mają hasło <b>DemoSklep123!</b>, o ile nie zostało zmienione."),
    p("Do CRUD utwórz osobne konto i produkt testowy; nie usuwaj kont demo. Używaj oddzielnych profili przeglądarki dla klienta i administratora albo wylogowuj się między rolami. Nowe konto aktywuj przez Mailpit."),
    *table([
        ["Wymaganie prowadzącego", "Gdzie w przewodniku"],
        ["HTML5, CSS3, PHP 8.x, MariaDB, Laravel", "s. 2"],
        ["Rejestracja, logowanie, zapis i hashowanie haseł", "s. 3"],
        ["Co najmniej 2 role, różne strony po logowaniu", "s. 4 (są 3 role)"],
        ["CRUD użytkowników, lista i pojedynczy użytkownik", "s. 5"],
        ["Walidacja wymaganych pól", "s. 3, 5 i 8"],
        ["Koszyk w sesji, produkty, zamawianie", "s. 6 i 7"],
        ["Aktywacja e-mailem, regex, SQL Injection", "s. 4 i 8"],
        ["Dodatki: profil, reset, zdjęcia, API i testy", "s. 6, 9 i 10"],
    ], [WIDTH-150, 150]),
    p("Samodzielność, oddanie w terminie i obrona są obowiązkami studenta; aplikacja ich nie potwierdza. Przećwicz wyjaśnianie kodu. Według Twojej informacji prowadzący nie wymaga modyfikacji kodu bez Internetu ze starego opisu kursu.", "SmallPL"),
])
show.append(page("2. Technologia, baza i MVC") + [
    p("Pokaż katalog sklepu oraz terminal", "SubPL"),
    *code('php --version\nphp artisan --version\nphp artisan migrate:status\nphp artisan route:list'),
    p("Wynik: PHP 8.x, Laravel Framework 12.x, wykonane migracje i lista tras. W phpMyAdmin otwórz bazę bytegear: users, categories, products, orders, order_items oraz sessions."),
    *files([
        ("composer.json", "php, ext-gd, laravel/framework"),
        ("config/database.php", "mysql oraz env('DB_DATABASE')"),
        ("database/migrations/0001_01_01_000000_create_users_table.php", "users, sessions, password_reset_tokens"),
        ("database/migrations/2026_10_06_000001_create_shop_tables.php", "role, products, orders, order_items"),
        ("database/seeders/ShopSeeder.php", "run()"),
    ]),
    p("<b>Wyjaśnij:</b> .env ma lokalne DB_HOST, DB_DATABASE, DB_USERNAME i DB_PASSWORD. Driver mysql obsługuje MariaDB. Migracje tworzą strukturę, seeder dodaje dane pokazowe. Na publicznym ekranie pokazuj .env.example zamiast sekretów swojego .env."),
    p("Jeden przykład przepływu MVC", "SubPL"),
    p("Otwórz " + link("http://127.0.0.1:8002/") + ". Wpisz nazwę produktu w wyszukiwarce."),
    *files([
        ("routes/web.php", "CatalogController::class, 'index'"),
        ("app/Http/Controllers/CatalogController.php", "index() i view('catalog.index'"),
        ("app/Models/Product.php", "category()"),
        ("resources/views/catalog/index.blade.php", "produkty i formularz filtrów"),
        ("resources/views/layouts/app.blade.php", "DOCTYPE, asset('css/shop.css'), @yield"),
        ("public/css/shop.css", "style i @media"),
    ]),
    p("<b>Powiedz:</b> trasa kieruje żądanie do kontrolera, kontroler odczytuje modele z bazy i przekazuje dane do Blade, który generuje HTML5. CSS3 nadaje wygląd. Obrazy i style są lokalne; nie trzeba npm."),
])
show.append(page("3. Rejestracja i logowanie") +
    block("A. Nowe konto i zapis użytkownika", "/rejestracja",
        "Jako gość utwórz konto, np. pokaz@example.test, z hasłem PokazSklepu123! i potwierdzeniem. Za każdym kolejnym pokazem użyj nowego adresu.",
        "Powstaje rekord users z rolą customer i niepotwierdzonym e-mailem. Wiadomość czeka w Mailpit. Nieaktywne konto nie może jeszcze zamawiać.",
        [("resources/views/auth/register.blade.php", "POST i @csrf"), ("app/Http/Controllers/AuthController.php", "register(), validate(), role = customer, User::create"), ("app/Models/User.php", "casts() i password => hashed")],
        "Hasło jest hashowane jednokierunkowo, a nie zapisywane jawnie. Cast modelu działa przy rejestracji i tworzeniu konta w panelu. Rola jest ustalana na serwerze; formularz rejestracji nie może nadać admina.") +
    block("B. Logowanie i wylogowanie", "/logowanie",
        "Spróbuj błędnego hasła, potem poprawnego. Aktywuj nowe konto według s. 4 albo użyj konta demo. Na końcu kliknij Wyloguj.",
        "Błędne dane nie logują. Poprawne dane tworzą zalogowaną sesję, a przycisk Wyloguj ją unieważnia.",
        [("app/Http/Controllers/AuthController.php", "login(): Auth::attempt(), regenerate(); logout(): invalidate()"), ("config/auth.php", "guards.web, providers.users")],
        "Auth::attempt sprawdza hasło względem hasha. Po logowaniu zmieniamy identyfikator sesji. Wylogowanie jest żądaniem POST z tokenem CSRF.") + [
        p("Pokaż walidację już na tym etapie", "SubPL"),
        p("W formularzu rejestracji wpisz niepoprawny e-mail, różne potwierdzenie lub hasło abc. Pokaż komunikaty. W kodzie wskaż required, email, unique, min, max, confirmed oraz regex. Walidacja serwera działa także wtedy, gdy ktoś ominie HTML required."),
        p("W phpMyAdmin można pokazać, że users.password zawiera hash, a nie hasło PokazSklepu123!. Nie wyświetlaj prywatnych kont ani eksportu danych na publicznym nagraniu.", "SmallPL"),
    ])
show.append(page("4. Aktywacja i poziomy uprawnień") +
    block("A. Potwierdzenie adresu e-mail", "/konto",
        "Zaloguj się na nowo zarejestrowane konto. Otwórz " + link("http://127.0.0.1:8025/", "Mailpit") + ", znajdź jego wiadomość i kliknij Aktywuj konto. Jeśli trzeba się zalogować, użyj tego samego konta.",
        "Znika ekran oczekiwania, email_verified_at otrzymuje datę. Link ma podpis i ważność 60 minut. Można wysłać go ponownie.",
        [("app/Models/User.php", "implements MustVerifyEmail"), ("app/Services/AccountMail.php", "verify()"), ("app/Http/Controllers/VerificationController.php", "notice(), verify(), send(), fulfill()"), ("routes/web.php", "signed, verified, verification.verify"), ("app/Providers/AppServiceProvider.php", "VerifyEmail::toMailUsing")],
        "To rzeczywisty mechanizm aktywacji przez wiadomość e-mail, z lokalnym odbiornikiem SMTP. Mailpit nie dostarcza wiadomości do Internetu. Gdy prowadzący wymaga zewnętrznej skrzynki, ustaw prawdziwy SMTP w .env; mechanizm aktywacji pozostaje ten sam.") +
    block("B. Trzy role i różne ekrany", "/konto",
        "Kolejno zaloguj customer@example.test, moderator@example.test i admin@example.test. Za każdym razem pokaż stronę po logowaniu. Jako klient wpisz ręcznie /panel/users i /panel/products.",
        "Klient widzi konto i zamówienia, moderator panel produktów, administrator panel użytkowników. Próba wejścia klienta do panelu pracownika kończy się 403.",
        [("app/Http/Controllers/OrderController.php", "dashboard() i match"), ("app/Http/Middleware/RequireRole.php", "handle(), abort_unless(), 403"), ("bootstrap/app.php", "alias role"), ("routes/web.php", "role:admin,moderator oraz role:admin")],
        "Uprawnienia są sprawdzane na serwerze. Ukrycie linku w menu nie wystarcza. Trzy role spełniają wymaganie co najmniej dwóch poziomów.")
)
show.append(page("5. CRUD użytkowników w panelu") +
    block("A. Lista, szczegóły, dodawanie i edycja", "/panel/users",
        "Zaloguj administratora. Pokaż listę i przycisk szczegółów wybranego konta. Dodaj konto crud-pokaz@example.test, następnie zmień jego imię i rolę. Użyj własnego nieużywanego adresu, jeśli taki już istnieje.",
        "Lista zawiera nowe konto; szczegóły pokazują pojedynczego użytkownika. Edycja zapisuje zmianę. Nowe konto otrzymuje hash hasła i wiadomość aktywacyjną.",
        [("app/Http/Controllers/AdminUserController.php", "index(), show(), create()/store(), edit()/update(), data()"), ("resources/views/admin/users/index.blade.php", "lista i działania"), ("resources/views/admin/users/show.blade.php", "pojedynczy użytkownik"), ("resources/views/admin/users/form.blade.php", "pola formularza"), ("routes/web.php", "resource('users'")],
        "CRUD to Create, Read, Update, Delete. Formularze i zapis są osobnymi akcjami. Tę samą ochronę hasła zapewnia model User. Zmiana adresu e-mail wymaga ponownej aktywacji.") +
    block("B. Usunięcie i kontrola błędów", "/panel/users",
        "Usuń tylko nowo utworzone konto testowe i pokaż jego brak na liście. Spróbuj dodać konto z istniejącym e-mailem albo niepoprawnymi danymi.",
        "Duplikat i błędne dane są odrzucane. Administrator nie może usunąć siebie ani pozbawić aplikacji ostatniego administratora. Historyczne zamówienia nie znikają po usunięciu konta.",
        [("app/Http/Controllers/AdminUserController.php", "destroy(), data(), DB::transaction"), ("database/migrations/2026_10_06_000001_create_shop_tables.php", "nullOnDelete() dla user_id"), ("lang/pl/validation.php", "polskie komunikaty"), ("resources/views/layouts/app.blade.php", "$errors->any()")],
        "Kontrola danych i uprawnień działa w PHP. Walidacja zapobiega niepoprawnemu zapisowi, a klucze obce chronią relacje. Historia zakupu jest niezależna od dalszego istnienia konta.")
)
show.append(page("6. Produkty, zdjęcia i koszyk") +
    block("A. Dodanie, edycja i wycofanie produktu", "/panel/products",
        "Jako moderator lub admin dodaj BG Pokaz USB (SKU POKAZ-USB-1, cena 49.90, stan 5, dowolna kategoria). Opcjonalnie wgraj własne JPG/PNG/WebP. Pokaż go w katalogu, edytuj cenę, potem wycofaj i przywróć aktywność.",
        "Produkt jest zapisany i widoczny w sklepie. Po wycofaniu znika z oferty. Pozostaje w panelu i bazie, a stare zamówienia nie tracą pozycji.",
        [("app/Http/Controllers/AdminProductController.php", "store(), update(), destroy(), data()"), ("app/Services/ProductImage.php", "store()"), ("app/Models/Product.php", "imagePath()"), ("app/Http/Controllers/CatalogController.php", "where('active', true), image()")],
        "Usuwanie ze sklepu oznacza wycofanie active=false, z zachowaniem historii. Zdjęcia są walidowane do 4 MB i przetwarzane przez GD. Własne pliki są w storage/app/private/product-images, obrazy demo w public/images/products.") +
    block("B. Koszyk przechowywany w sesji", "/koszyk",
        "W katalogu dodaj dostępny produkt. Zmień liczbę sztuk, odśwież stronę i usuń pozycję. Ponownie dodaj produkt, aby przygotować zakup. Koszyk może działać również bez logowania.",
        "Po odświeżeniu pozycje zostają w bieżącej sesji. Ilość i suma się zmieniają. Za duża ilość lub brak stanu są odrzucane.",
        [("app/Http/Controllers/CartController.php", "add(), update(), destroy(), session()->put('cart'"), ("app/Services/Cart.php", "rows(), total()"), ("config/session.php", "driver"), ("resources/views/cart.blade.php", "formularze ilości i usuwania")],
        "W sesji trzymamy ID i ilości. Ceny są odczytywane z bazy; klient nie może wysłać własnej ceny. SESSION_DRIVER=database oznacza zapis sesji w MariaDB, ale nadal jest to koszyk w sesji.")
)
show.append(page("7. Zamawianie i historia zakupów") +
    block("A. Klient składa zamówienie", "/zamowienie",
        "Zaloguj aktywnego klienta, dodaj produkt do koszyka i przejdź do zamówienia. Wpisz dane testowe, np. Test Pokazu, Pokazowa 1, 00-001 Warszawa. Najpierw wpisz błędny kod, potem popraw i zatwierdź zakup.",
        "Błędny kod pocztowy daje komunikat. Poprawny zakup zapisuje zamówienie i pozycje, zmniejsza stan produktu i czyści koszyk. Potwierdzenie trafia do " + link("http://127.0.0.1:8025/", "Mailpit") + ".",
        [("app/Http/Controllers/OrderController.php", "checkout(), store(), validate(), DB::transaction(), lockForUpdate()"), ("resources/views/checkout.blade.php", "pola i checkout_token"), ("app/Services/OrderMail.php", "send()"), ("resources/views/mail/order-confirmation.blade.php", "treść potwierdzenia")],
        "Ceny w bazie są całkowitymi groszami. Transakcja zapisuje całość lub wycofuje zmiany; blokady chronią magazyn. Formularz ma ochronę przed powtórnym zapisem. Awaria SMTP nie cofa zakupu; nieudaną wysyłkę można ponowić.") +
    block("B. Lista, status i anulowanie", "/zamowienia",
        "Otwórz szczegóły własnego zamówienia. Dla jednego nowego zamówienia potwierdź anulowanie. Dla drugiego pokaż w /panel/orders zmianę na realizowane, potem zrealizowane; wróć do widoku klienta.",
        "Historia pozostaje. Anulowanie nowego zamówienia zwraca zapas tylko raz. Zamówienia w realizacji klient już nie anuluje. Pracownik może zmieniać status do następnego etapu.",
        [("app/Http/Controllers/OrderController.php", "index(), show(), cancel(), increment('stock'"), ("app/Http/Controllers/AdminOrderController.php", "update()"), ("app/Models/Order.php", "STATUSES, items(), user()"), ("resources/views/orders/show.blade.php", "szczegóły i przyciski")],
        "order_items przechowuje historyczne nazwy i ceny. Zmiana lub wycofanie produktu nie zmienia starego zamówienia. show() i cancel() kontrolują właściciela; klient nie ogląda cudzych zakupów.")
)
show.append(page("8. Walidacja, regex i bezpieczeństwo") + [
    p("A. Wyrażenia regularne - wymaganie na wyższą ocenę", "SubPL"),
    p("Pokaż niepoprawne hasło w " + link("http://127.0.0.1:8002/rejestracja", "rejestracji") + ", kod pocztowy w zamówieniu oraz małe litery/spację w SKU produktu. Reguły PHP odrzucają dane; polskie błędy są widoczne na stronie."),
    *files([("app/Http/Controllers/AuthController.php", "register(): regex hasła"), ("app/Http/Controllers/OrderController.php", "store(): regex kodu pocztowego"), ("app/Http/Controllers/AdminProductController.php", "data(): regex SKU")]),
    *code('Hasło: /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).+$/s\nKod pocztowy: /^[0-9]{2}-[0-9]{3}$/\nSKU: /^[A-Z0-9-]+$/'),
    p("Hasło wymaga małej i wielkiej litery oraz cyfry; długość sprawdzają min/max. Kod ma format 00-000; SKU zawiera wielkie litery, cyfry i myślniki."),
    p("B. SQL Injection - wymaganie na wyższą ocenę", "SubPL"),
    p("Na stronie " + link("http://127.0.0.1:8002/", "katalogu") + " wpisz w wyszukiwarce tekst <b>' OR 1=1 --</b>. Nie uzyskujesz dostępu do bazy; to tylko szukana wartość. Kod i test są lepszym dowodem niż sam widok pustej listy."),
    *files([("app/Http/Controllers/CatalogController.php", "index(): where('name', 'like'...), validate(sort), match"), ("tests/Feature/ShopTest.php", "test_html_is_escaped_and_search_injection_does_not_change_database")]),
    p("<b>Powiedz:</b> Eloquent wiąże wartości jako parametry SQL. Znaki % są częścią wartości wyszukiwania, a nie doklejonym kodem SQL. Sortowanie wybiera kolumnę z zamkniętej listy. Nie budujemy zapytań SQL przez sklejanie danych użytkownika."),
    p("C. CSRF, XSS i kontrola dostępu", "SubPL"),
    *files([("resources/views/auth/register.blade.php", "@csrf"), ("resources/views/catalog/show.blade.php", "{{ ... }}"), ("routes/web.php", "auth, auth.session, verified, role, throttle")]),
    p("CSRF chroni akcje formularzy przed obcymi żądaniami. Blade {{ ... }} ucieka HTML, ograniczając XSS. Middleware sprawdza login, aktywację i rolę przed kontrolerem. To osobne mechanizmy, a nie trzy nazwy ochrony przed SQL Injection."),
])
show.append(page("9. Dodatki do zaprezentowania") +
    block("A. Profil i zmiana hasła", "/profil",
        "Jako klient zmień imię. Opcjonalnie zmień e-mail, wpisując bieżące hasło, i pokaż nową wiadomość aktywacyjną. Zmień hasło na inną poprawną wartość; zanotuj je, jeśli modyfikujesz konto demo.",
        "Imię zapisuje się. Zmiana e-maila wymaga ponownego potwierdzenia. Zmiana hasła sprawdza stare hasło i usuwa pozostałe sesje, zachowując bieżącą.",
        [("app/Http/Controllers/ProfileController.php", "edit(), update(), password()"), ("resources/views/auth/profile.blade.php", "formularze profilu i hasła")],
        "Użytkownik edytuje tylko dozwolone pola; nie może nadać sobie roli. Nowe hasło przechodzi walidację i hashowanie.") +
    block("B. Odzyskanie zapomnianego hasła", "/zapomniane-haslo",
        "Wyloguj się. Podaj adres konta testowego, otwórz wiadomość w Mailpit i ustaw nowe hasło. Zaloguj się nowym hasłem.",
        "Token działa jednorazowo przez 60 minut. Poprzednie sesje są unieważniane. Odpowiedź formularza nie ujawnia, czy dany e-mail istnieje.",
        [("app/Http/Controllers/PasswordController.php", "email(), update(), Password::sendResetLink(), Password::reset()"), ("config/auth.php", "passwords.users: expire, throttle")],
        "Reset korzysta z brokera Laravel i tabeli password_reset_tokens. To inny mechanizm niż aktywacja konta.") + [
        p("C. Wygląd, wyszukiwanie i konfiguracja marki", "SubPL"),
        p("W katalogu pokaż kategorię, sortowanie, filtr dostępności, ceny i paginację. Zwęź okno lub użyj widoku urządzenia w przeglądarce. Sprawdź nawigację Tab. Style są w public/css/shop.css, layout w resources/views/layouts/app.blade.php."),
        p("Nazwa sklepu pochodzi z APP_NAME w .env przez config/app.php i config('app.name'). Po zmianie wykonaj php artisan config:clear. Nie trzeba zmieniać nazwy bazy ani repozytorium. Modele BG i wizualizacje są demonstracyjne; sklep nie przetwarza rzeczywistych płatności.", "SmallPL"),
    ])
show.append(page("10. API, testy i krótki plan obrony") + [
    p("API NBP - dodatek pokazujący materiał z kursu", "SubPL"),
    p("Na szczegółach dowolnego produktu kliknij cenę w EUR. Przykład dla początkowych danych: " + link("http://127.0.0.1:8002/produkty/1/euro") + ". Jeśli produkt o ID 1 nie istnieje lub jest wycofany, wybierz inny z katalogu."),
    *files([("app/Http/Controllers/CatalogController.php", "euro(), timeout(8)"), ("resources/views/catalog/euro.blade.php", "kurs EUR i komunikat o błędzie")]),
    p("Kurs pochodzi z API NBP. Brak Internetu wyświetla komunikat; nie blokuje zamawiania w PLN. API i automatyczne testy są dodatkami, a nie osobnymi minimalnymi wymaganiami sklepu."),
    p("Testy automatyczne", "SubPL"),
    *code('php artisan test'),
    p("Ostatni sprawdzony wynik tej wersji: <b>41 testów, 310 asercji</b>. Liczby mogą zmienić się po dalszych zmianach. Testy używają SQLite w pamięci, oddzielnie od MariaDB sklepu."),
    *files([("phpunit.xml", "DB_CONNECTION=sqlite, DB_DATABASE=:memory:"), ("tests/Feature/ShopTest.php", "CRUD, role, koszyk, zamówienia, błędne dane"), ("tests/Feature/AccountSecurityTest.php", "aktywacja, podpis, reset, sesje"), ("tests/Feature/ShopExtensionsTest.php", "profil, zdjęcia, anulowanie i wiadomości")]),
    p("Prezentacja minimum w około 10-15 minut", "SubPL"),
    p("1. Katalog i MVC; wersje PHP/Laravel oraz tabele.<br/>2. Rejestracja z błędnymi danymi, poprawny zapis, hash i Mailpit.<br/>3. Logowanie trzech ról i odmowa dostępu klientowi.<br/>4. Admin: lista, szczegóły, dodanie, edycja i usunięcie konta testowego.<br/>5. Moderator: dodanie i wycofanie produktu.<br/>6. Klient: koszyk, zakup, historia; regex i zapytanie parametryzowane.<br/>7. Jeśli jest czas: profil, anulowanie, wiadomość, API i testy."),
    p("Co świadomie wyjaśnić prowadzącemu", "SubPL"),
    p("Produkty wycofujemy z oferty zamiast kasować historię. Poczta podczas pokazu trafia do lokalnego Mailpit; zewnętrzny SMTP jest opcją konfiguracji. Każdą funkcję umiej wskazać jako: trasa - kontroler - model/baza - widok. Ocenę i zaliczenie przyznaje prowadzący po prezentacji, nie sam wynik testów."),
    p("Repozytorium: " + link("https://github.com/pzx119592/WSB_Projekt_ByteGear") + ". Dokumenty PDF i instrukcje TXT można pobrać razem z kodem.", "SmallPL"),
])
build("Prezentacja_projektu_ByteGear.pdf", "Prezentacja projektu ByteGear - wymagania i kod", show)
print("Generated:")
for path in OUT.glob("*.pdf"):
    print(path)
