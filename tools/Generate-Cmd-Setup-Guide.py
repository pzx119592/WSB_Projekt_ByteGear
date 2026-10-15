"""Plain numbered CMD setup instructions. Requires ReportLab to generate."""
from pathlib import Path
from xml.sax.saxutils import escape
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, PageBreak

ROOT=Path(__file__).resolve().parents[1]
OUT=ROOT/'output/pdf/Instalacja_od_zera_CMD_ByteGear.pdf'
for name,file in [('Text','segoeui.ttf'),('Bold','segoeuib.ttf'),('Mono','consola.ttf')]:
    pdfmetrics.registerFont(TTFont(name,str(Path('C:/Windows/Fonts')/file)))
pdfmetrics.registerFontFamily('Text',normal='Text',bold='Bold',italic='Text',boldItalic='Bold')
styles={
 'text':ParagraphStyle('text',fontName='Text',fontSize=10.5,leading=14,spaceAfter=7),
 'title':ParagraphStyle('title',fontName='Bold',fontSize=14,leading=18,spaceAfter=13),
 'code':ParagraphStyle('code',fontName='Mono',fontSize=9,leading=12,spaceAfter=8,leftIndent=10),
}
def p(t,kind='text'):return Paragraph(t,styles[kind])
def command(t):return p(escape(t),'code')
def step(n,t):return p(f'<b>{n}.</b> '+t)
def link(url,label):return '<a href="'+url+'"><u>'+label+'</u></a>'
pages=[[
 p('Instalacja sklepu od zera - Windows CMD','title'),
 p('Rób kroki po kolei. Każde polecenie wpisuj osobno i naciskaj Enter. Jeśli pojawi się błąd, zatrzymaj się na tym kroku.'),
 step(1,'W przeglądarce otwórz '+link('https://www.apachefriends.org/download.html','apachefriends.org/download.html')+'. Pobierz XAMPP dla Windows z PHP 8.2. Jeśli XAMPP już jest, pomiń instalację.'),
 step(2,'Uruchom instalator. Zostaw Apache, PHP, MySQL/MariaDB i phpMyAdmin. Zainstaluj w <b>C:\\xampp</b>.'),
 step(3,'Otwórz XAMPP Control Panel. Kliknij Start przy <b>Apache</b> i <b>MySQL</b>. Obie pozycje mają być zielone.'),
 step(4,'W Notatniku otwórz <b>C:\\xampp\\php\\php.ini</b>. Ctrl+F: znajdź extension=gd. Usuń średnik z początku linii. Tak samo zrób z extension=zip. Mają wyglądać tak:'),
 command('extension=gd'),command('extension=zip'),
 p('Zapisz Ctrl+S. Zamknij Notatnik. Nie dopisuj drugiej aktywnej kopii tych samych linii. W XAMPP kliknij Stop przy Apache, potem Start.'),
 step(5,'W przeglądarce pobierz '+link('https://getcomposer.org/Composer-Setup.exe','getcomposer.org/Composer-Setup.exe')+'. Uruchom instalator Composer. Gdy pyta o PHP, wskaż <b>C:\\xampp\\php\\php.exe</b>. Proxy zostaw niewłączone, chyba że uczelnia podała inne ustawienia. Dokończ instalację.'),
 step(6,'Zamknij stare terminale. Naciśnij <b>Windows+R</b>, wpisz <b>cmd</b> i Enter. To ma być Wiersz polecenia, nie okno z początkiem PS.'),
 step(7,'Do CMD wklej poniższe polecenie i naciśnij Enter:'),
 command('set "PATH=C:\\xampp\\php;%PATH%"'),
 step(8,'Do CMD wpisuj osobno te polecenia. Po każdym Enter. Powinny wyświetlić się wersje PHP i Composer:'),
 command('php --version'),command('composer --version'),
],[
 p('Pobranie projektu i instalacja Laravel','title'),
 step(9,'W przeglądarce otwórz '+link('https://github.com/pzx119592/WSB_Projekt_ByteGear','github.com/pzx119592/WSB_Projekt_ByteGear')+'. Kliknij <b>Code &gt; Download ZIP</b>.'),
 step(10,'W Eksploratorze plików wypakuj ZIP. Folder z aplikacją przenieś do C:\\xampp\\htdocs i nazwij <b>bytegear</b>. Pliki artisan i composer.json mają być bezpośrednio w tym folderze. Sprawdź tę ścieżkę:'),
 command('C:\\xampp\\htdocs\\bytegear\\artisan'),
 p('Jeśli jest dodatkowy folder WSB_Projekt_ByteGear-main, przenieś jego zawartość bezpośrednio do bytegear.'),
 step(11,'Wróć do CMD. Wpisz i naciśnij Enter:'),
 command('cd /d C:\\xampp\\htdocs\\bytegear'),
 step(12,'W CMD utwórz plik ustawień. Jeśli masz już własny .env, pomiń ten krok i go nie nadpisuj:'),
 command('copy .env.example .env'),
 step(13,'W CMD wpisz dokładnie <b>install</b>, nie isntall. Poczekaj, aż pobieranie się zakończy:'),
 command('composer install'),
 p('To instaluje Laravel i biblioteki naszego sklepu. Nie wykonuj laravel new ani composer update. Jeśli widzisz błąd ext-gd, wróć do kroku 4, zapisz php.ini i powtórz composer install.'),
 step(14,'W CMD wykonaj osobno:'),
 command('composer check-platform-reqs'),command('php artisan --version'),
 p('Wymagania mają być spełnione, a ostatni wynik ma zawierać Laravel Framework 12.x.x. Brak vendor/autoload.php oznacza, że krok 13 nie zakończył się poprawnie. Najpierw popraw błąd z composer install.'),
],[
 p('Baza danych i ustawienia .env','title'),
 step(15,'W przeglądarce otwórz '+link('http://localhost/phpmyadmin/','http://localhost/phpmyadmin/')+'. Kliknij <b>Nowa</b>. Nazwa bazy: <b>bytegear</b>. Porównywanie: <b>utf8mb4_unicode_ci</b>. Kliknij Utwórz. Jeśli baza już zawiera dane, nie kasuj jej; do nowej instalacji utwórz np. bytegear_pokaz.'),
 step(16,'Jeśli masz domową kopię .sql: wybierz pustą bazę po lewej &gt; Import &gt; Wybierz plik &gt; wskaż .sql &gt; Importuj/Wykonaj. Poczekaj na komunikat sukcesu. Jeśli nie masz SQL, pomiń import - użyjesz danych przykładowych w kroku 22.'),
 step(17,'Wróć do CMD. Otwórz ustawienia poleceniem:'),command('notepad .env'),
 step(18,'Teraz <b>w Notatniku, nie w CMD</b>, znajdź i popraw poniższe linie. Resztę pliku zostaw. DB_DATABASE musi pasować do bazy z kroku 15. Puste hasło dotyczy standardowego XAMPP; gdy uczelnia ma inne dane, wpisz właściwe.'),
 command('APP_URL=http://127.0.0.1:8002<br/>DB_CONNECTION=mysql<br/>DB_HOST=127.0.0.1<br/>DB_PORT=3306<br/>DB_DATABASE=bytegear<br/>DB_USERNAME=root<br/>DB_PASSWORD=<br/>SESSION_DRIVER=database<br/>CACHE_STORE=database'),
 step(19,'W tym samym Notatniku sprawdź ustawienia poczty. Nie dodawaj duplikatów istniejących linii:'),
 command('MAIL_MAILER=smtp<br/>MAIL_SCHEME=null<br/>MAIL_HOST=127.0.0.1<br/>MAIL_PORT=1025<br/>MAIL_USERNAME=null<br/>MAIL_PASSWORD=null<br/>MAIL_FROM_ADDRESS="sklep@bytegear.test"<br/>MAIL_FROM_NAME="${APP_NAME}"'),
 p('Zapisz <b>Ctrl+S</b> i zamknij Notatnik. Plik ma nazywać się .env, nie .env.txt.'),
],[
 p('Uruchomienie i logowanie','title'),
 step(20,'W CMD wygeneruj klucz tylko dla nowej instalacji. Jeśli przeniosłeś domowy .env z istniejącym APP_KEY, pomiń to polecenie:'),
 command('php artisan key:generate'),
 step(21,'W CMD wykonaj:'),command('php artisan config:clear'),
 step(22,'Wybierz <b>jedno</b> polecenie. Po imporcie SQL wpisz:'),command('php artisan migrate'),
 p('Jeśli nie importowałeś SQL i chcesz przykładowe konta oraz produkty, zamiast powyższego wpisz:'),command('php artisan migrate --seed'),
 p('Nie używaj migrate:fresh - kasuje dane.'),
 step(23,'W CMD uruchom pocztę. To polecenie uruchamia skrypt PowerShell z poziomu CMD. Przy pierwszym starcie pobiera Mailpit:'),
 command('powershell.exe -NoProfile -ExecutionPolicy Bypass -File tools\\Start-Mailpit.ps1'),
 step(24,'W CMD uruchom sklep i <b>zostaw to okno otwarte</b>:'),command('php artisan serve --host=127.0.0.1 --port=8002'),
 step(25,'W przeglądarce otwórz sklep: '+link('http://127.0.0.1:8002/','http://127.0.0.1:8002/')+'. Logowanie jest pod /logowanie. Wiadomości aktywacyjne i potwierdzenia znajdziesz lokalnie: '+link('http://127.0.0.1:8025/','http://127.0.0.1:8025/')+'. Nie przychodzą do Gmaila.'),
 step(26,'Zaloguj wybrane konto. Hasło wszystkich to <b>DemoSklep123!</b>:<br/>Klient: <b>customer@example.test</b><br/>Moderator: <b>moderator@example.test</b><br/>Administrator: <b>admin@example.test</b><br/>Po imporcie SQL obowiązują hasła z tej kopii, także te zmienione ręcznie.'),
 step(27,'Następnym razem włącz MySQL w XAMPP i w nowym CMD powtórz kroki <b>7, 11, 23, 24</b>. Apache włącz również, jeśli chcesz phpMyAdmin. Nie instaluj ponownie bibliotek, nie kopiuj .env ani nie generuj nowego klucza przy zwykłym starcie.'),
 p('Kopia domowa: phpMyAdmin &gt; bytegear &gt; Eksport &gt; Szybki &gt; SQL. Własne zdjęcia kopiuj osobno z folderu storage\\app\\private\\product-images do tej samej lokalizacji projektu. Przykładowe grafiki są na GitHubie.'),
]]
# Keep literal line breaks in settings without escaping them into visible HTML.
# Commands containing <br/> are assembled as ordinary paragraphs here.
for pg in pages:
    for i,item in enumerate(pg):
        if isinstance(item,Paragraph) and '&lt;br/&gt;' in item.text:
            pg[i]=Paragraph(item.text.replace('&lt;br/&gt;','<br/>'),styles['code'])
story=[]
for i,pg in enumerate(pages):
    if i:story.append(PageBreak())
    story.extend(pg)
def footer(c,d):
    c.setFont('Text',8);c.drawRightString(A4[0]-42,24,str(d.page))
SimpleDocTemplate(str(OUT),pagesize=A4,leftMargin=42,rightMargin=42,topMargin=36,bottomMargin=40,title='ByteGear - prosta instrukcja instalacji w CMD',author='ByteGear').build(story,onFirstPage=footer,onLaterPages=footer)
print(OUT)
