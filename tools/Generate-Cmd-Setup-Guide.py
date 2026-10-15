"""Create the step-by-step Windows CMD setup PDF. Requires ReportLab only for generation."""
from pathlib import Path
from xml.sax.saxutils import escape
from reportlab.lib import colors
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, PageBreak, Table, TableStyle, KeepTogether

ROOT=Path(__file__).resolve().parents[1]
OUT=ROOT/'output/pdf/Instalacja_od_zera_CMD_ByteGear.pdf'
OUT.parent.mkdir(parents=True,exist_ok=True)
for n,f in [('Body','segoeui.ttf'),('Bold','segoeuib.ttf'),('Code','consola.ttf')]:
    pdfmetrics.registerFont(TTFont(n,str(Path('C:/Windows/Fonts')/f)))
pdfmetrics.registerFontFamily('Body',normal='Body',bold='Bold',italic='Body',boldItalic='Bold')
W=A4[0]-92
S={
    'body':ParagraphStyle('body',fontName='Body',fontSize=10.8,leading=15,spaceAfter=8,textColor='#172B46'),
    'h1':ParagraphStyle('h1',fontName='Bold',fontSize=22,leading=27,spaceAfter=13,textColor='#172B46'),
    'h2':ParagraphStyle('h2',fontName='Bold',fontSize=12,leading=16,spaceBefore=6,spaceAfter=7,textColor='#2455DB'),
    'small':ParagraphStyle('small',fontName='Body',fontSize=9.3,leading=12.5,spaceAfter=6,textColor='#50627A'),
    'code':ParagraphStyle('code',fontName='Code',fontSize=8.8,leading=12.2,textColor='#172B46'),
}
def p(t,s='body'):return Paragraph(t,S[s])
def h(t):return p(t,'h2')
def link(u,label=None):return '<a href="'+u+'" color="#2455DB"><u>'+escape(label or u)+'</u></a>'
def box(text):
    rows=[[p(escape(line).replace(' ','&#160;'),'code')] for line in text.splitlines()]
    t=Table(rows,colWidths=[W])
    t.setStyle(TableStyle([('BACKGROUND',(0,0),(-1,-1),'#EEF3FC'),('LEFTPADDING',(0,0),(-1,-1),10),('RIGHTPADDING',(0,0),(-1,-1),10),('TOPPADDING',(0,0),(-1,-1),4),('BOTTOMPADDING',(0,0),(-1,-1),4)]))
    return [t,Spacer(1,8)]
def cmd(text,description=None):
    result=[]
    if description:result.append(p(description))
    result+=box(text)
    return result
def page(n,title,where):return [p(f'{n}. {title}','h1'),p('<b>GDZIE:</b> '+where,'small')]
pages=[]
pages.append(page(1,'Zacznij tutaj','Windows - instalatory, przeglądarka i CMD')+[
    p('Instrukcja od zera na uczelnianym lub drugim komputerze z Windows. Obejmuje XAMPP, PHP, Composer, nasz Laravel, bazę MariaDB, import SQL oraz pocztę Mailpit. Przy pierwszej instalacji potrzebny jest Internet.'),
    h('Wybierz odpowiedni moment'),
    p('<b>Wszystko instalujesz pierwszy raz?</b> Idź od strony 1 do 7.<br/><b>Sklep jest już zainstalowany?</b> Użyj krótkiego startu na stronie 7.<br/><b>Masz błąd?</b> Sprawdź stronę 8 i zatrzymaj się na poleceniu, które go wywołało.'),
    h('Jak otworzyć właściwy terminal'),
    p('Naciśnij <b>Windows+R</b>, wpisz <b>cmd</b> i Enter. Tytuł okna to zwykle „Wiersz polecenia”. Polecenia w tym PDF-ie są przygotowane do CMD.'),
    *box('C:\\Users\\TwojaNazwa>'),
    p('Powyżej jest przykład początku wiersza. <b>Nie kopiuj tego do terminala.</b> Jeśli widzisz na początku <b>PS C:\\...</b>, masz PowerShell: otwórz CMD przez Windows+R zgodnie z instrukcją.'),
    h('Jak wklejać polecenia'),
    p('Każde niebieskie pole z poleceniem kopiuj <b>osobno</b>. Wklej przez Ctrl+V lub prawy przycisk myszy, naciśnij Enter i poczekaj na wynik. Nie sklejaj kilku poleceń w jeden wiersz. Ustawienia .env z s. 5 wklejasz do Notatnika, a nie CMD.'),
    h('Co będzie potrzebne'),
    p('XAMPP w <b>C:\\xampp</b>, PHP 8.2, Composer i projekt w <b>C:\\xampp\\htdocs\\bytegear</b>. VS Code jest przydatny do pokazu kodu, ale nie jest wymagany do samego uruchomienia. Git i Node.js/npm nie są potrzebne do opisanej metody z ZIP-em.'),
    p('Spis: s. 2 - XAMPP/PHP/Composer; s. 3 - pobranie i biblioteki; s. 4 - baza i SQL; s. 5 - .env; s. 6 - przygotowanie i start; s. 7 - konta i następny start; s. 8 - błędy i kopia danych.','small'),
])
pages.append(page(2,'XAMPP, PHP i Composer','Przeglądarka, instalatory oraz Notatnik; na końcu nowe CMD')+[
    h('A. XAMPP'),
    p('Pobierz '+link('https://www.apachefriends.org/download.html','XAMPP dla Windows z PHP 8.2')+'. Zainstaluj w <b>C:\\xampp</b>; potrzebne składniki to Apache, PHP, MySQL/MariaDB i phpMyAdmin. Jeśli są już zainstalowane, nie instaluj ich ponownie.'),
    p('Otwórz <b>XAMPP Control Panel</b>. Kliknij <b>Start</b> przy Apache i MySQL. Obie pozycje powinny być zielone. MySQL w tym panelu oznacza nasz serwer MariaDB.'),
    h('B. Włącz GD i ZIP przed Composerem'),
    p('Otwórz <b>C:\\xampp\\php\\php.ini</b> w Notatniku. Ctrl+F: szukaj extension=gd, potem extension=zip. Usuń średnik z początku tych linii, jeśli występuje. Mają wyglądać tak:'),
    *box('extension=gd\nextension=zip'),
    p('Nie dodawaj drugiej kopii aktywnej linii. Zapisz <b>Ctrl+S</b>. Jeśli zapis jest zabroniony, uruchom Notatnik jako administrator i otwórz ten plik. W XAMPP zatrzymaj i uruchom Apache. Nowe wywołanie PHP w CMD odczyta zapisane ustawienia.'),
    h('C. Zainstaluj Composer'),
    p('Pobierz '+link('https://getcomposer.org/Composer-Setup.exe','Composer-Setup.exe')+' i uruchom. Gdy instalator pyta o PHP, wskaż <b>C:\\xampp\\php\\php.exe</b>. Proxy zostaw niewłączone, chyba że uczelnia podała własną konfigurację. Dokończ instalację.'),
    p('<b>Zamknij stare CMD i otwórz nowe</b> przez Windows+R, cmd, Enter. Wykonaj każde z poniższych poleceń osobno:'),
    *cmd('set "PATH=C:\\xampp\\php;%PATH%"'),
    *cmd('php --version'),
    *cmd('composer --version'),
    p('<b>Oczekiwany wynik:</b> wersja PHP 8.2 i wersja Composer. Jeśli polecenie nie jest rozpoznawane, nie idź dalej - zobacz s. 8. Oficjalna '+link('https://getcomposer.org/doc/00-intro.md#installation-windows','instrukcja instalacji Composer')+'.','small'),
])
pages.append(page(3,'Pobierz sklep i zainstaluj Laravel','Przeglądarka i Eksplorator plików; potem CMD')+[
    h('A. Pobranie projektu'),
    p('Otwórz '+link('https://github.com/pzx119592/WSB_Projekt_ByteGear','repozytorium sklepu')+'. Kliknij <b>Code &gt; Download ZIP</b>. W Eksploratorze kliknij pobrany ZIP prawym przyciskiem &gt; Wyodrębnij wszystkie.'),
    p('Przenieś wypakowany folder projektu do C:\\xampp\\htdocs i nazwij go <b>bytegear</b>. Sprawdź, czy pliki artisan, composer.json i .env.example są bezpośrednio w tym folderze. Prawidłowa ścieżka:'),
    *box('C:\\xampp\\htdocs\\bytegear\\artisan'),
    p('Folder bytegear nie może zawierać kolejnego folderu WSB_Projekt_ByteGear-main, w którym dopiero leżą pliki aplikacji.'),
    h('B. Każde polecenie poniżej wklej osobno do CMD'),
    *cmd('cd /d C:\\xampp\\htdocs\\bytegear'),
    *cmd('set "PATH=C:\\xampp\\php;%PATH%"'),
    *cmd('copy .env.example .env','Pierwsza instalacja: utwórz lokalne ustawienia. Jeśli masz już swój .env, pomiń kopiowanie; nie nadpisuj go.'),
    *cmd('composer install','Pobierz Laravel i wymagane biblioteki. To może potrwać kilka minut.'),
    p('<b>To właśnie instaluje Laravel naszego sklepu.</b> Nie twórz nowego projektu przez laravel new. Prawidłowa pisownia to <b>install</b>, nie isntall. Nie wykonuj composer update ani pomijania wymagań.'),
    *cmd('composer check-platform-reqs'),
    *cmd('php artisan --version'),
    p('<b>Oczekiwany wynik:</b> wymagania spełnione i Laravel Framework 12.x.x. Jeśli composer install zakończył się błędem, zatrzymaj się - kolejne polecenia Artisan mogą zgłaszać brak vendor/autoload.php.','small'),
])
pages.append(page(4,'Baza danych i import SQL','Przeglądarka - phpMyAdmin')+[
    h('A. Utwórz bazę'),
    p('Włącz Apache i MySQL w XAMPP. Otwórz '+link('http://localhost/phpmyadmin/')+'. Po lewej kliknij <b>Nowa</b>, wpisz <b>bytegear</b>, wybierz <b>utf8mb4_unicode_ci</b> i kliknij <b>Utwórz</b>.'),
    p('Jeśli istnieje już baza bytegear z danymi, nie kasuj jej. Do nowego importu użyj innej pustej bazy, np. bytegear_pokaz, i wpisz tę nazwę w DB_DATABASE na następnej stronie.'),
    h('B. Wybierz jeden z dwóch wariantów'),
    p('<b>WARIANT A - mam plik .sql z domowej bazy:</b><br/>1. Po lewej wybierz pustą bazę utworzoną przed chwilą.<br/>2. Na górze kliknij <b>Import</b>.<br/>3. Kliknij <b>Wybierz plik</b> i wskaż kopię .sql.<br/>4. Format pozostaw <b>SQL</b>; kliknij na dole <b>Importuj / Wykonaj</b>.<br/>5. Poczekaj na komunikat powodzenia. Po lewej powinny być m.in. users, products i orders.'),
    p('Import struktury i danych zachowuje konta, hasła, stany i zamówienia z chwili wykonania kopii. Upewnij się, że kopia pochodzi z aplikacji <b>bytegear</b>, a nie z wcześniejszych ćwiczeń.'),
    p('<b>WARIANT B - nie mam pliku SQL:</b><br/>Zostaw pustą bazę. Na stronie 6 wybierzesz polecenie, które utworzy tabele i doda przykładowe konta oraz produkty. Sklep będzie działać, ale bez Twoich domowych zamówień i innych własnych danych.'),
    h('Jak zrobić kopię w domu?'),
    p('W domowym phpMyAdmin kliknij bazę <b>bytegear &gt; Eksport &gt; Szybki &gt; SQL &gt; Eksportuj</b>. Domyślny szybki eksport obejmuje strukturę i dane. Plik przenieś prywatnie, np. pendrivem. Nie publikuj kopii kont i zamówień na publicznym GitHubie.'),
    h('A zdjęcia dodane przez panel?'),
    p('Nie są częścią SQL. Przenieś osobno zawartość folderu:'),
    *box('C:\\xampp\\htdocs\\bytegear\\storage\\app\\private\\product-images'),
    p('Wklej ją na drugim komputerze do tej samej ścieżki, zachowując nazwy plików. Przykładowe grafiki ze sklepu są już na GitHubie. Jeśli nie dodawałeś własnych zdjęć, ten krok pomiń.'),
])
pages.append(page(5,'Ustawienia bazy i poczty','Notatnik - plik .env; nie wklejaj ustawień do terminala')+[
    p('Będąc w folderze projektu, wpisz w CMD to jedno polecenie:'),
    *cmd('notepad .env'),
    p('Otworzy się plik utworzony na stronie 3. Jeśli Notatnik pyta o utworzenie nowego pustego pliku, anuluj i sprawdź folder oraz kopiowanie .env.example.'),
    h('A. W Notatniku znajdź i popraw odpowiednie linie'),
    p('Nie zastępuj całej zawartości .env. Popraw istniejące ustawienia tak, aby miały właściwe wartości. Nie dodawaj ich wielokrotnie.'),
    *box('APP_NAME=ByteGear\nAPP_URL=http://127.0.0.1:8002\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=bytegear\nDB_USERNAME=root\nDB_PASSWORD=\nSESSION_DRIVER=database\nCACHE_STORE=database'),
    p('DB_DATABASE to nazwa bazy z poprzedniej strony. Puste DB_PASSWORD pasuje do standardowego lokalnego XAMPP. Jeśli uczelnia podała inne konto, hasło lub port, użyj tych danych.'),
    h('B. Poczta lokalna Mailpit'),
    *box('MAIL_MAILER=smtp\nMAIL_SCHEME=null\nMAIL_HOST=127.0.0.1\nMAIL_PORT=1025\nMAIL_USERNAME=null\nMAIL_PASSWORD=null\nMAIL_FROM_ADDRESS="sklep@bytegear.test"\nMAIL_FROM_NAME="${APP_NAME}"'),
    p('Mailpit odbiera aktywacje i potwierdzenia lokalnie. Wiadomości oglądasz w przeglądarce pod 127.0.0.1:8025; nie trafią do Gmaila ani innej prawdziwej skrzynki.'),
    p('<b>Zapisz Ctrl+S i zamknij Notatnik.</b> Plik ma nazywać się .env, nie .env.txt. Nie publikuj tego pliku; może zawierać sekrety.','small'),
])
pages.append(page(6,'Przygotuj i uruchom aplikację','CMD - nadal C:\\xampp\\htdocs\\bytegear')+[
    h('A. Klucz i odczyt ustawień'),
    *cmd('php artisan key:generate','Na świeżej instalacji wykonaj raz:'),
    p('Jeśli przeniosłeś domowy .env z istniejącym APP_KEY, pomiń generowanie i zachowaj ten klucz. Na kolejnych uruchomieniach sklepu nie generuj go ponownie.','small'),
    *cmd('php artisan config:clear'),
    h('B. Wybierz tylko jedno polecenie dla bazy'),
    *cmd('php artisan migrate','WARIANT A: zaimportowałeś domowy SQL:'),
    *cmd('php artisan migrate --seed','WARIANT B: masz pustą bazę i chcesz dane przykładowe:'),
    p('W wariancie B powstają tabele, 18 produktów, 6 kategorii i konta demo. <b>Nie wykonuj migrate:fresh</b>: kasuje tabele i dane. Jeśli widzisz błąd bazy, zatrzymaj się i wróć do stron 4-5.'),
    h('C. Uruchom pocztę'),
    *cmd('powershell.exe -NoProfile -ExecutionPolicy Bypass -File tools\u005cStart-Mailpit.ps1'),
    p('Polecenie wklejasz do CMD. Uruchamia ono gotowy skrypt przez PowerShell; nie musisz przełączać terminala. Przy pierwszym starcie pobiera Mailpit z Internetu, później używa lokalnej kopii.'),
    h('D. Uruchom sklep'),
    *cmd('php artisan serve --host=127.0.0.1 --port=8002'),
    p('<b>Oczekiwany wynik:</b> informacja o serwerze pod http://127.0.0.1:8002. <b>Zostaw to okno CMD otwarte.</b> Nie wpisuj w nim kolejnych poleceń podczas pracy serwera; Ctrl+C zatrzymuje sklep.'),
])
pages.append(page(7,'Sprawdź sklep i zapamiętaj następny start','Przeglądarka; sekcja „następny start” ponownie w CMD')+[
    h('Otwórz te adresy'),
    p('<b>Sklep:</b> '+link('http://127.0.0.1:8002/')+'<br/><b>Logowanie:</b> '+link('http://127.0.0.1:8002/logowanie')+'<br/><b>Poczta Mailpit:</b> '+link('http://127.0.0.1:8025/')+'<br/><b>Baza phpMyAdmin:</b> '+link('http://localhost/phpmyadmin/')),
    h('Konta demonstracyjne'),
    p('<b>Klient:</b> customer@example.test<br/><b>Moderator:</b> moderator@example.test<br/><b>Administrator:</b> admin@example.test<br/><b>Hasło wszystkich: DemoSklep123!</b>'),
    p('Dotyczy kont utworzonych przez seeder. Po imporcie domowego SQL obowiązują hasła z tej kopii. Jeżeli wcześniej zmieniłeś hasło konta demo, użyj zmienionego. Konta demo są aktywne; nowe konto potwierdzasz przez wiadomość w Mailpit.'),
    h('Następnym razem - nie instalujesz wszystkiego od nowa'),
    p('Włącz MySQL w XAMPP (Apache także, jeśli chcesz phpMyAdmin). Otwórz CMD i wykonaj pojedynczo:'),
    *cmd('cd /d C:\\xampp\\htdocs\\bytegear'),
    *cmd('set "PATH=C:\\xampp\\php;%PATH%"'),
    *cmd('powershell.exe -NoProfile -ExecutionPolicy Bypass -File tools\u005cStart-Mailpit.ps1'),
    *cmd('php artisan serve --host=127.0.0.1 --port=8002'),
    p('Otwórz sklep w przeglądarce. Nie kopiuj ponownie .env, nie generuj nowego klucza i nie importuj ponownie bazy przy zwykłym uruchomieniu.'),
    p('Po pobraniu bibliotek i Mailpit podstawowe funkcje działają offline. Przeliczenie EUR z API NBP wymaga Internetu. Instrukcja pokazu: output/pdf/Prezentacja_krok_po_kroku_ByteGear.pdf.','small'),
])
pages.append(page(8,'Błędy, które już przerobiliśmy','Najpierw odczytaj pierwszy właściwy błąd w terminalu')+[
    h('Brak vendor/autoload.php'),
    p('Biblioteki nie zostały poprawnie zainstalowane. W folderze projektu wykonaj <b>composer install</b> i poczekaj. Jeśli ono zgłasza błąd, rozwiąż go przed kolejnymi poleceniami Artisan.'),
    h('Composer: extension ext-gd is missing'),
    p('W C:\\xampp\\php\\php.ini usuń średnik przed <b>extension=gd</b> i zapisz. Wykonaj ponownie composer install. Nie używaj composer update ani --ignore-platform-reqs do obchodzenia tego błędu.'),
    h('Command "isntall" is not defined'),
    p('To literówka. Poprawnie: <b>composer install</b>. Jeśli Composer proponuje poprawne „install”, wpisz yes i Enter.'),
    h('Set-Location / A positional parameter cannot be found'),
    p('To PowerShell albo kilka poleceń sklejonych w jeden wiersz. Naciśnij Windows+R, wpisz cmd i Enter. Wklejaj osobno każde polecenie z tej instrukcji. W CMD ustawiamy PATH przez <b>set</b>, a nie $env:Path.'),
    h('composer is not recognized / php is not recognized'),
    p('Po instalacji Composera otwórz nowe CMD. Dla PHP wykonaj <b>set "PATH=C:\\xampp\\php;%PATH%"</b>. Sprawdź, czy PHP istnieje w tej ścieżce. Jeśli Composer nadal nie jest rozpoznawany, sprawdź zakończenie jego instalacji.'),
    h('Connection refused / Access denied / Unknown database'),
    p('Włącz MySQL w XAMPP. Sprawdź nazwę bazy, login, hasło i port w .env. Zapisz, wykonaj <b>php artisan config:clear</b> i ponów nieudane polecenie migracji.'),
    h('Strona nieosiągalna albo port 8002 zajęty'),
    p('Samo włączenie Apache nie uruchamia Artisan. Sprawdź, czy sklep już działa, zanim uruchomisz drugi serwer. Jeśli wybierasz inny port, zmień też APP_URL w .env i wykonaj config:clear.'),
    h('Brak wiadomości / link aktywacyjny odrzucony'),
    p('Uruchom Mailpit, poproś o nowy link i otwórz go jako właściwy użytkownik. Używaj adresu 127.0.0.1 zgodnego z APP_URL. Link aktywacyjny jest ważny 60 minut.'),
    p('Przy dalszym błędzie pokaż pełny wynik ostatniego nieudanego polecenia. Nie wklejaj haseł ani APP_KEY. Ta instrukcja nie zmienia globalnych ustawień bezpieczeństwa Windows.','small'),
])

def footer(c,d):
    c.setStrokeColor(colors.HexColor('#D8E1EE'));c.line(46,42,A4[0]-46,42)
    c.setFillColor(colors.HexColor('#50627A'));c.setFont('Body',8)
    c.drawString(46,28,'ByteGear | instalacja od zera w CMD | 15.10.2026')
    c.drawRightString(A4[0]-46,28,str(d.page))
story=[]
for i,content in enumerate(pages):
    if i:story.append(PageBreak())
    story.extend(content)
SimpleDocTemplate(str(OUT),pagesize=A4,leftMargin=46,rightMargin=46,topMargin=40,bottomMargin=58,title='ByteGear - instalacja od zera w CMD',author='ByteGear - instrukcja uruchomienia').build(story,onFirstPage=footer,onLaterPages=footer)
print(OUT)
