# Historia zamówień i wycofywanie produktów

Wycofanie produktu z oferty nie usuwa historii jego zakupu.
AdminProductController::destroy ustawia active=false; katalog pokazuje tylko aktywne produkty.
Produkt można przywrócić przez edycję w panelu.

OrderController::store zapisuje w order_items kopię nazwy, ceny w groszach oraz ilości.
Zmiana lub wycofanie produktu nie zmienia treści wcześniejszego zamówienia.
Klucz product_id jest nullable z nullOnDelete; nawet ewentualne fizyczne usunięcie
produktu pozostawiłoby te historyczne dane pozycji zamówienia.

To dwie niezależne decyzje: zachowanie danych zamówienia oraz pozostawienie rekordu
produktu. Historia może pozostać także przy fizycznym usunięciu produktu.
Archiwizacja jest dobrym rozwiązaniem dla wycofanych produktów, które mogą wrócić do oferty.
Nie oznacza to, że każdy duży sklep przechowuje wszystkie produkty bezterminowo.

Przykład dojrzałej platformy: Shopify udostępnia archiwizację i trwałe usuwanie
produktów oraz wyświetla w zamówieniu historyczne dane zakupu.

- [Archiwizacja i usuwanie produktów Shopify](https://help.shopify.com/en/manual/products/add-update-products)
- [Historyczne dane zamówienia Shopify](https://help.shopify.com/en/manual/fulfillment/managing-orders/managing-order-details)

Scenariusz prezentacji: złóż zamówienie jako klient, zmień nazwę/cenę produktu
w panelu, wycofaj produkt, a następnie otwórz stare zamówienie. Jego nazwa, cena,
ilość i kwota pozostają zgodne z momentem zakupu.
