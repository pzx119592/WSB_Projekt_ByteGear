# Przegląd wyglądu i obsługi

Zachowano jasny sklep, lokalne grafiki i prostą nawigację. Uporządkowano profil,
przesyłanie zdjęć i anulowanie zamówienia. Nie dodawano nowych bibliotek UI.

Zmiany: systemowa czcionka, przyciski i pola o wysokości co najmniej 44 px,
widoczny fokus klawiatury, link pomijający menu, polska paginacja bez listy punktowanej,
wyróżnienie aktywnego profilu, etykiety statusów i formularze w jednej kolumnie na telefonie.
Katalog na telefonie od 360 px pokazuje dwie kolumny produktów.
Wersje WebP 18 grafik zajmują łącznie około 1,4 MB zamiast około 27,8 MB PNG.
Obrazy katalogu są wczytywane leniwie; oryginalne PNG zachowano do edycji.
Przy ograniczeniu ruchu w systemie płynne przewijanie jest wyłączane.

Sprawdzono profil i katalog przy szerokości 390 px: brak poziomego przewijania
całej strony. Szerokie tabele mają własny kontener przewijania.
Zakup, pocztę i anulowanie sprawdzono w przeglądarce na lokalnej MariaDB.
To przegląd wybranych elementów, nie pełny audyt wszystkich kryteriów WCAG.

Źródła odniesienia:

- [W3C — etykiety pól](https://www.w3.org/WAI/tutorials/forms/labels/)
- [W3C — widoczność fokusu](https://www.w3.org/WAI/WCAG22/Understanding/focus-appearance)
- [W3C — wielkość obszarów dotykowych](https://www.w3.org/WAI/WCAG21/Understanding/target-size)
