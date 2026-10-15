"""A separate, beginner-friendly presentation PDF. Requires ReportLab on Windows."""
from pathlib import Path
from xml.sax.saxutils import escape
from reportlab.lib import colors
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, PageBreak, Table, TableStyle

ROOT = Path(__file__).resolve().parents[1]
OUT = ROOT / 'output/pdf/Prezentacja_krok_po_kroku_ByteGear.pdf'
OUT.parent.mkdir(parents=True, exist_ok=True)
for n, f in [('Regular','segoeui.ttf'),('Bold','segoeuib.ttf'),('Mono','consola.ttf')]:
    pdfmetrics.registerFont(TTFont(n, str(Path('C:/Windows/Fonts') / f)))
pdfmetrics.registerFontFamily('Regular', normal='Regular', bold='Bold', italic='Regular', boldItalic='Bold')
W = A4[0] - 92
BLUE = colors.HexColor('#2455DB')
ST = {
    'body': ParagraphStyle('body', fontName='Regular', fontSize=11, leading=15.5, spaceAfter=8, textColor='#172B46'),
    'h1': ParagraphStyle('h1', fontName='Bold', fontSize=22, leading=27, spaceAfter=14, textColor='#172B46'),
    'h2': ParagraphStyle('h2', fontName='Bold', fontSize=12.5, leading=17, spaceBefore=7, spaceAfter=7, textColor=BLUE),
    'small': ParagraphStyle('small', fontName='Regular', fontSize=9.3, leading=13.1, spaceAfter=6, textColor='#50627A'),
    'path': ParagraphStyle('path', fontName='Mono', fontSize=9.2, leading=13, spaceAfter=4, textColor='#172B46'),
}

def p(s, style='body'):
    return Paragraph(s, ST[style])

def url(path, text=None):
    target = 'http://127.0.0.1:8002' + path
    return p('<a href="'+target+'" color="#2455DB"><u>'+escape(text or target)+'</u></a>')

def title(n, s):
    return [p(f'{n}. {s}', 'h1')]

def head(s):
    return p(s, 'h2')

def location(path, find, meaning):
    assert (ROOT / path).is_file(), path
    full = 'C:\\xampp\\htdocs\\bytegear\\' + path.replace('/', '\\')
    # Break after a directory separator, never in a filename; full path stays copyable.
    formatted = escape(full).replace('\\', '\\<wbr/>')
    source = 'https://github.com/pzx119592/WSB_Projekt_ByteGear/blob/main/' + path
    rows = [[p(formatted, 'path')], [p('<b>Ctrl+F:</b> '+escape(find), 'small')], [p(meaning, 'small')]]
    t=Table(rows, colWidths=[W])
    t.setStyle(TableStyle([('BACKGROUND',(0,0),(-1,-1),'#EEF3FC'),('LEFTPADDING',(0,0),(-1,-1),10),('RIGHTPADDING',(0,0),(-1,-1),10),('TOPPADDING',(0,0),(-1,-1),3),('BOTTOMPADDING',(0,0),(-1,-1),3)]))
    return [t, p('<a href="'+source+'" color="#2455DB"><u>Otwórz ten plik na GitHub</u></a>', 'small'), Spacer(1,4)]

def say(s):
    return [head('Możesz powiedzieć własnymi słowami'), p('„'+s+'”')]

def more(path, find, meaning):
    return [p('<b>Jeśli nauczyciel pyta, jak to działa:</b> '+meaning, 'small')] + location(path,find,'To obsługa działania w PHP, a nie plik z wyglądem strony.')

pages=[]
pages.append(title(1,'Jak korzystać z tej instrukcji') + [
    p('To ściąga do pokazu sklepu, krok po kroku. Najpierw coś klikasz w przeglądarce, potem pokazujesz pasujący plik. Nie musisz uczyć się nazw plików na pamięć.'),
    head('Najpierw otwórz cały projekt w VS Code'),
    p('1. Uruchom Visual Studio Code.<br/>2. Kliknij <b>Plik &gt; Otwórz folder</b> (File &gt; Open Folder).<br/>3. Wybierz <b>C:\\xampp\\htdocs\\bytegear</b> i kliknij Wybierz folder.<br/>4. Po lewej zobaczysz foldery projektu. Rozwijaj je strzałkami.'),
    p('<b>Szybszy sposób:</b> naciśnij <b>Ctrl+P</b>, wklej pełną ścieżkę z niebieskiego pola w PDF i Enter. Potem <b>Ctrl+F</b> i wpisz podany fragment. Nie trzeba szukać konkretnego numeru linii.'),
    head('Trzy rzeczy, które warto rozróżniać'),
    p('<b>Widok (.blade.php):</b> treść strony - nagłówki, pola, przyciski. To tutaj znajdziesz HTML. Blade to HTML z dodatkami Laravel, np. wstawianiem nazwy produktu.<br/><b>CSS (.css):</b> kolory, czcionki, wielkość i rozmieszczenie elementów.<br/><b>Kontroler (.php):</b> działanie po kliknięciu, np. sprawdzenie hasła lub zapis zamówienia.'),
    p('W Laravelu nie wszystko mieści się w jednym index.html. Treść strony, wspólne menu, wygląd i obsługa formularza są w osobnych plikach.'),
    head('Przygotuj przeglądarkę'),
    url('/'),
    p('W XAMPP włącz MySQL, a sklep uruchom osobno przez Artisan. Szczegółowy start jest w pierwszym PDF-ie. Gdy strona jest nieosiągalna, samo włączenie Apache nie wystarczy.'),
    p('Konta do pokazu: <b>customer@example.test</b>, <b>moderator@example.test</b>, <b>admin@example.test</b>.<br/>Wspólne hasło: <b>DemoSklep123!</b>, jeśli nie zmieniałeś go ręcznie.'),
    p('Strony 2-3: mapa HTML i CSS. Strony 4-9: prezentacja zaliczenia. Strony 10-12: dodatki, proste odpowiedzi i końcowa lista kontrolna.', 'small'),
])
pages.append(title(2,'Gdzie jest HTML poszczególnych stron?') + [
    p('Wszystkie poniższe pliki są w folderze <b>resources\\views</b>. To najszybsza mapa, gdy usłyszysz: „Proszę pokazać kod tej strony”.'),
    *location('resources/views/catalog/index.blade.php','Małe zmiany.','<b>Strona główna:</b> duży nagłówek, wstęp, kategorie, wyszukiwanie i lista produktów.'),
    *location('resources/views/layouts/app.blade.php','<header','<b>Wspólna część stron:</b> początek HTML, nazwa sklepu, menu, stopka i miejsce na komunikaty błędów.'),
    *location('resources/views/auth/login.blade.php','Zaloguj się','<b>Logowanie:</b> pola e-mail i hasło oraz przycisk logowania.'),
    *location('resources/views/auth/register.blade.php','<form','<b>Rejestracja:</b> pola nowego konta i potwierdzenie hasła.'),
    *location('resources/views/cart.blade.php','@section','<b>Koszyk:</b> pozycje, liczba sztuk i podsumowanie.'),
    p('Dalsze pliki: <b>checkout.blade.php</b> to formularz zamówienia; <b>orders\\index.blade.php</b> to historia, a <b>orders\\show.blade.php</b> to szczegóły zamówienia. Wszystkie są pod resources\\views.', 'small'),
    p('<b>Uwaga:</b> welcome.blade.php to pozostawiony domyślny widok Laravel. Nie jest aktualną stroną główną sklepu.', 'small'),
])
pages.append(title(3,'Pokaż stronę główną i jej wygląd') + [
    head('W przeglądarce'), url('/'),
    p('Pokaż nagłówek „Małe zmiany. Większy komfort.”, menu i produkty. Wybierz kategorię, wyszukaj produkt i zmień sortowanie. To dobry początek prezentacji.'),
    head('W VS Code: skąd wziął się napis na stronie?'),
    *location('resources/views/catalog/index.blade.php','<h1>','Pokaż nagłówek pomiędzy &lt;h1&gt; i &lt;/h1&gt;. Niżej &lt;p&gt; oznacza akapit, a &lt;a&gt; link. Nie musisz zmieniać pliku podczas obrony.'),
    head('W VS Code: gdzie są kolory i rozmiary?'),
    *location('public/css/shop.css','.hero h1','To główny plik wyglądu sklepu. .hero h1 określa duży nagłówek, .header górne menu, a .product-grid układ produktów.'),
    p('W CSS <b>font-size</b> oznacza wielkość pisma, <b>color</b> kolor tekstu, <b>background</b> tło, a <b>padding</b> odstęp wewnątrz elementu. Część reguł jest zapisana w długich liniach; <b>Alt+Z</b> w VS Code włącza zawijanie tekstu.'),
    head('Jak strona korzysta z CSS?'),
    *location('resources/views/layouts/app.blade.php',"asset('css/shop.css')",'Linia &lt;link rel="stylesheet" ...&gt; dołącza CSS. Plik jest w public\\css, ale w adresie zasobu nie dopisujemy public.'),
    *say('Treść strony głównej mam w widoku catalog/index.blade.php. Menu i stopka są wspólne dla stron. Wygląd ustalam w public/css/shop.css.'),
])
pages.append(title(4,'Rejestracja, logowanie i hasła') + [
    head('W przeglądarce - najpierw jako osoba wylogowana'), url('/rejestracja'),
    p('1. Załóż konto, np. <b>pokaz15@example.test</b>, z imieniem Jan Pokaz.<br/>2. Użyj hasła <b>PokazSklepu123!</b> i wpisz je ponownie w potwierdzeniu.<br/>3. Po rejestracji zaloguj się. Przy kolejnym pokazie wybierz nowy adres, jeśli ten jest już zajęty.<br/>4. Aktywację pokaż na następnej stronie tej instrukcji.'),
    head('Gdzie jest HTML tych formularzy?'),
    *location('resources/views/auth/register.blade.php','<form','Tu widać pola rejestracji. &lt;input&gt; to pole, &lt;button&gt; to przycisk.'),
    *location('resources/views/auth/login.blade.php','type="password"','Tu jest formularz logowania. type="password" ukrywa wpisywane znaki na ekranie.'),
    head('A gdzie sprawdzane są dane?'),
    *location('app/Http/Controllers/AuthController.php','public function register','register zapisuje konto; login sprawdza dane, a logout wylogowuje. validate oznacza sprawdzenie poprawności pól.'),
    *location('app/Models/User.php',"'password' => 'hashed'",'To właściwe zabezpieczenie hasła w bazie. Ukrywanie znaków w polu formularza nie zastępuje hashowania.'),
    *say('Użytkownik rejestruje konto w formularzu. PHP sprawdza dane, a hasło zapisuje jako jednokierunkowy hash. Przy logowaniu Laravel sprawdza, czy podane hasło pasuje. Nie przechowuję jawnych haseł.'),
])
pages.append(title(5,'Aktywacja e-maila i trzy role') + [
    head('Najpierw aktywuj nowe konto'),
    p('1. Zaloguj nowe konto i otwórz „Moje konto”.<br/>2. Otwórz <a href="http://127.0.0.1:8025/" color="#2455DB"><u>http://127.0.0.1:8025/</u></a>.<br/>3. Znajdź wiadomość do swojego adresu i kliknij „Aktywuj konto”.<br/>4. Wróć do sklepu. Ekran prośby o potwierdzenie powinien zniknąć.'),
    *location('resources/views/auth/verify-email.blade.php','Potwierdź','Tu jest HTML ekranu oczekiwania na potwierdzenie adresu.'),
    *location('app/Http/Controllers/VerificationController.php','public function verify','Ten plik obsługuje potwierdzenie. Link jest ważny 60 minut i nie można dowolnie zmieniać jego treści.'),
    p('<b>Mailpit</b> jest lokalną skrzynką testową: odbiera prawdziwą wiadomość wysłaną przez sklep, ale nie dostarcza jej do Gmaila itp. Gdy potrzebna jest zwykła skrzynka, zmieniamy ustawienia SMTP w .env.', 'small'),
    head('Następnie pokaż różne uprawnienia'), url('/konto'),
    p('Kolejno loguj trzy konta ze strony 1, wylogowując poprzednie:<br/><b>Klient</b> widzi swoje konto i zamówienia.<br/><b>Moderator</b> trafia do produktów.<br/><b>Administrator</b> trafia do użytkowników.'),
    *location('app/Http/Controllers/OrderController.php','public function dashboard','dashboard wybiera stronę po logowaniu zależnie od roli.'),
    *location('app/Http/Middleware/RequireRole.php','403','Ten plik odmawia dostępu osobom bez odpowiedniej roli. Jako klient wpisz ręcznie /panel/users: powinna pojawić się odmowa 403.'),
    *say('Są trzy poziomy uprawnień. Różnią się nie tylko menu: serwer też sprawdza, czy dana osoba może wejść do panelu.'),
])
pages.append(title(6,'Administrator zarządza użytkownikami') + [
    head('W przeglądarce - zaloguj administratora'), url('/panel/users'),
    p('1. Pokaż listę użytkowników.<br/>2. Wejdź w szczegóły jednej osoby.<br/>3. Kliknij „Dodaj użytkownika” i utwórz konto testowe z nowym adresem.<br/>4. Edytuj to konto, np. zmień imię, i pokaż wynik.<br/>5. Usuń wyłącznie to konto testowe. Nie usuwaj kont demonstracyjnych.'),
    head('Gdzie jest HTML panelu?'),
    *location('resources/views/admin/users/index.blade.php','Użytkownicy','Lista użytkowników i przyciski. index to tutaj po prostu lista.'),
    *location('resources/views/admin/users/show.blade.php','@section','Strona szczegółów jednej osoby. show oznacza pokazanie pojedynczego wpisu.'),
    *location('resources/views/admin/users/form.blade.php','<form','Formularz dodawania i edycji - ten sam plik obsługuje obie sytuacje.'),
    head('Gdzie jest zapis i usunięcie?'),
    *location('app/Http/Controllers/AdminUserController.php','public function store','store dodaje, update zmienia, destroy usuwa. index pobiera listę, a show jedną osobę. Hasło nadal zabezpiecza model User.'),
    *say('Panel realizuje CRUD: dodawanie, odczyt, zmianę i usuwanie. Administrator widzi wszystkich użytkowników i szczegóły jednej osoby. Aplikacja chroni przed usunięciem własnego konta administratora i ostatniego administratora.'),
])
pages.append(title(7,'Dodawanie i usuwanie produktów') + [
    head('W przeglądarce - moderator lub administrator'), url('/panel/products'),
    p('1. Kliknij dodanie produktu.<br/>2. Wpisz np. <b>BG Mysz Pokazowa</b>, kod <b>POKAZ-MYSZ-15</b>, cenę <b>49.90</b>, stan <b>5</b> i wybierz kategorię. Uzupełnij opis, pozostaw produkt aktywny.<br/>3. Możesz dodać własne zdjęcie JPG, PNG lub WebP (do 4 MB).<br/>4. Zapisz i pokaż produkt w sklepie.<br/>5. Wróć do panelu, zmień cenę, zapisz, a następnie wycofaj produkt z oferty.'),
    head('Gdzie jest HTML?'),
    *location('resources/views/admin/products/form.blade.php','Dodaj produkt','Formularz nazwy, ceny, magazynu i zdjęcia. Tu pokazujesz pola produktu.'),
    *location('resources/views/admin/products/index.blade.php','@section','Lista produktów dostępna dla pracownika sklepu.'),
    *location('resources/views/catalog/show.blade.php','@section','Strona pojedynczego produktu, którą ogląda kupujący.'),
    head('Gdzie działa dodawanie i wycofanie?'),
    *location('app/Http/Controllers/AdminProductController.php','public function destroy','store dodaje, update zmienia, destroy ustawia produkt jako nieaktywny. Pliki przesłanych zdjęć są w storage\\app\\private\\product-images.'),
    *say('Usunięcie ze sklepu oznacza wycofanie z oferty. Rekord zostaje w bazie, żeby zachować historię zamówień. Można go później ponownie aktywować. Klient nie ma dostępu do tego panelu.'),
])
pages.append(title(8,'Koszyk i złożenie zamówienia') + [
    head('W przeglądarce - klient'), url('/koszyk'),
    p('1. W sklepie dodaj dostępny produkt do koszyka.<br/>2. Zmień liczbę sztuk i odśwież stronę: zawartość pozostaje.<br/>3. Usuń pozycję, a potem ponownie dodaj produkt.<br/>4. Zaloguj aktywnego klienta i przejdź do zamówienia.'),
    *location('resources/views/cart.blade.php','@section','HTML koszyka: nazwy, ilości, przyciski i suma.'),
    *location('app/Http/Controllers/CartController.php',"put('cart'",'Tu zapisujemy koszyk w sesji. Sesja pamięta zawartość dla tej przeglądarki; nie trzeba zakładać konta, żeby dodać produkt.'),
    head('Dokończ zakup'), url('/zamowienie'),
    p('Wypełnij np. Jan Pokaz, Pokazowa 1, <b>00-001</b>, Warszawa. Kliknij „Złóż zamówienie”. To zakup demonstracyjny: pieniądze nie są pobierane. Po zapisie pokaż numer, pozycje i sumę.'),
    *location('resources/views/checkout.blade.php','Dokończ zamówienie','HTML formularza adresu i przycisku zakupu.'),
    *location('app/Http/Controllers/OrderController.php','public function store','Tutaj powstaje zamówienie. Program sprawdza dostępność, bierze ceny z bazy, zapisuje zakup i zmniejsza magazyn.'),
    *say('Koszyk jest przechowywany w sesji. Zamówienie zapisuje się już na stałe w bazie. Cena pochodzi z bazy sklepu, więc klient nie może sobie wpisać dowolnej ceny w formularzu.'),
])
pages.append(title(9,'Historia, status i anulowanie') + [
    head('W przeglądarce - klient'), url('/zamowienia'),
    p('Otwórz szczegóły właśnie złożonego zamówienia. Pokaż produkty, kwotę, adres i status. W Mailpit pokaż również wiadomość z potwierdzeniem zakupu.'),
    *location('resources/views/orders/index.blade.php','@section','HTML historii zamówień - lista zakupów klienta.'),
    *location('resources/views/orders/show.blade.php','Zamówienie #','HTML szczegółów jednego zamówienia. W tym pliku są także przyciski zmiany statusu i anulowania, widoczne dla odpowiednich osób.'),
    head('Wybierz dwa osobne zamówienia do pokazania'),
    p('<b>Pierwsze, jako klient:</b> zaznacz potwierdzenie i kliknij „Anuluj zamówienie”, dopóki jest nowe. Pozostaje w historii, a towar wraca do magazynu.<br/><b>Drugie, jako pracownik:</b> otwórz poniższy panel, wybierz zamówienie i rozpocznij realizację. Następnie oznacz je jako zrealizowane.'),
    url('/panel/orders'),
    *location('app/Http/Controllers/OrderController.php','public function cancel','Anulowanie sprawdza właściciela i status zamówienia. Nie można w ten sposób anulować cudzego lub już realizowanego zakupu.'),
    *location('app/Http/Controllers/AdminOrderController.php','public function update','Obsługa kolejnych statusów przez moderatora lub administratora. Lista pracownika korzysta ze wspólnego widoku orders/index.blade.php.'),
    *say('Historia przechowuje nazwę i cenę z momentu zakupu. Późniejsza zmiana ceny lub wycofanie produktu nie zmienia starego zamówienia.'),
])
pages.append(title(10,'Profil i odzyskanie hasła') + [
    head('Pokaż profil zalogowanego klienta'), url('/profil'),
    p('Zmień samo imię i zapisz. Opcjonalnie pokaż zmianę e-maila lub hasła. Zmiana e-maila wymaga bieżącego hasła i ponownej aktywacji. Jeśli zmienisz hasło konta demo, zapisz sobie nowe.'),
    *location('resources/views/auth/profile.blade.php','<form','HTML edycji danych oraz zmiany hasła.'),
    *location('app/Http/Controllers/ProfileController.php','public function update','update zapisuje dane profilu, password zmienia hasło. Użytkownik nie może nadać sobie wyższych uprawnień.'),
    head('Pokaż „Nie pamiętam hasła” - po wylogowaniu'), url('/zapomniane-haslo'),
    p('Podaj e-mail konta testowego. Otwórz wiadomość w Mailpit, kliknij reset i ustaw nowe hasło. Zaloguj się nim. Link jest ważny 60 minut i działa jednorazowo.'),
    *location('resources/views/auth/forgot-password.blade.php','<form','HTML formularza, w którym podaje się e-mail do odzyskania hasła.'),
    *location('resources/views/auth/reset-password.blade.php','<form','HTML ustawienia nowego hasła po kliknięciu linku z wiadomości.'),
    *location('app/Http/Controllers/PasswordController.php','public function update','Obsługa resetu. Stare zalogowane sesje są unieważniane.'),
    p('To dodatki do sklepu. W pierwszej kolejności pokaż wymagane funkcje ze stron 4-8 oraz zabezpieczenia ze strony 11.', 'small'),
])
pages.append(title(11,'Błędne dane i zabezpieczenia prosto') + [
    head('1. Formularze nie mogą przyjmować wszystkiego'),
    p('Pokaż w rejestracji hasło <b>abc</b> albo niezgodne potwierdzenie. W zamówieniu wpisz kod pocztowy <b>123</b>. Popraw dane po pokazaniu błędów.'),
    *location('app/Http/Controllers/AuthController.php','validate','required = pole wymagane; email = poprawny format; min = minimalna długość; confirmed = zgodność z potwierdzeniem. Sprawdzanie odbywa się na serwerze, nie tylko w przeglądarce.'),
    head('2. Wyrażenie regularne, czyli wzór poprawnego tekstu'),
    *location('app/Http/Controllers/OrderController.php','regex:','Regex kodu pocztowego wymaga dwóch cyfr, myślnika i trzech cyfr: np. 00-001. Regex hasła w AuthController wymaga małej i wielkiej litery oraz cyfry.'),
    head('3. Ochrona przed SQL Injection'),
    *location('app/Http/Controllers/CatalogController.php',"where('name', 'like'",'Tu jest szukanie produktu. Laravel przekazuje tekst jako wartość do wyszukania, a nie jako polecenie dla bazy. Nie sklejamy poleceń SQL z wpisanym tekstem.'),
    p('Możesz wpisać w wyszukiwarce <b>\' OR 1=1 --</b>. To ma być potraktowane jako tekst, a nie instrukcja. Sam pusty wynik nie jest dowodem - wskaż również powyższy kod.'),
    head('4. Co oznaczają te dodatki w HTML?'),
    p('<b>@csrf</b> w formularzu dodaje zabezpieczenie przed niechcianym wysłaniem akcji z obcej strony.<br/><b>{{ $zmienna }}</b> wstawia dane do HTML tak, żeby wpisany kod HTML nie wykonał się jako część strony.'),
    *location('resources/views/layouts/app.blade.php','$errors->any()','Tu wyświetlane są komunikaty o błędach. Ich polskie treści są w lang\\pl\\validation.php.'),
    *say('Sprawdzam poprawność danych przed zapisem. Stosuję wzory dla hasła i kodu pocztowego. Zapytania do bazy są parametryzowane, a formularze mają token CSRF.'),
])
pages.append(title(12,'Ostatnie pytania i lista do zaliczenia') + [
    head('„Gdzie jest ustawiona baza lub nazwa sklepu?”'),
    p('Otwórz <b>C:\\xampp\\htdocs\\bytegear\\.env</b>. Nazwa sklepu to <b>APP_NAME</b>, adres to <b>APP_URL</b>, a baza to <b>DB_DATABASE</b>. Ustawienia poczty zaczynają się od <b>MAIL_</b>. Po zmianie wykonaj php artisan config:clear. Nie pokazuj na nagraniu haseł ani APP_KEY; publiczny wzór to .env.example.'),
    head('„Gdzie jest podłączony adres strony?”'),
    *location('routes/web.php',"Route::get('/'",'To spis adresów. Główne / prowadzi do CatalogController i jego części index, która wybiera widok catalog.index. Nie jest to plik HTML.'),
    head('„Czy projekt używa API i ma testy?”'),
    p('W szczegółach produktu kliknij przeliczenie ceny na EUR. Internet jest potrzebny do kursu NBP, ale nie do zakupów w PLN. Kod: <b>app\\Http\\Controllers\\CatalogController.php</b>, wyszukaj <b>function euro</b>. HTML tej strony: <b>resources\\views\\catalog\\euro.blade.php</b>.'),
    p('Testy są w <b>tests\\Feature</b>. Uruchamia je <b>php artisan test</b>. Sprawdzają wiele działań automatycznie i nie zmieniają domowej bazy. API i testy są dodatkami.'),
    head('Przed końcem pokazu upewnij się, że było'),
    p('1. Rejestracja, logowanie i miejsce hashowania haseł - s. 4.<br/>2. Aktywacja wiadomością i różne role - s. 5.<br/>3. Lista, szczegóły, dodanie, edycja i usunięcie użytkownika - s. 6.<br/>4. Dodanie i wycofanie produktu przez pracownika - s. 7.<br/>5. Koszyk w sesji oraz zamówienie klienta - s. 8.<br/>6. Walidacja, regex i zabezpieczenie zapytań do bazy - s. 11.<br/>7. HTML strony głównej, logowania oraz CSS - s. 2-4.'),
    p('Technologie projektu: HTML5, CSS3, PHP 8.x, Laravel 12 i MariaDB. Można je pokazać w kodzie widoków, composer.json oraz terminalu: php --version i php artisan --version.', 'small'),
    p('Nie ucz się gotowych zdań bez zrozumienia. Przećwicz pokaz, mówiąc własnymi słowami. Samodzielność, termin oddania i obrona zależą od Ciebie; ocenę przyznaje prowadzący.', 'small'),
])

def footer(c,d):
    c.setStrokeColor(colors.HexColor('#D8E1EE'))
    c.line(46,42,A4[0]-46,42)
    c.setFillColor(colors.HexColor('#50627A'))
    c.setFont('Regular',8)
    c.drawString(46,28,'ByteGear | prezentacja krok po kroku | 15.10.2026')
    c.drawRightString(A4[0]-46,28,str(d.page))

story=[]
for i,content in enumerate(pages):
    if i: story.append(PageBreak())
    story.extend(content)
SimpleDocTemplate(str(OUT),pagesize=A4,leftMargin=46,rightMargin=46,topMargin=40,bottomMargin=59,title='ByteGear - prezentacja krok po kroku dla początkujących',author='ByteGear - dokumentacja projektu').build(story,onFirstPage=footer,onLaterPages=footer)
print(OUT)
