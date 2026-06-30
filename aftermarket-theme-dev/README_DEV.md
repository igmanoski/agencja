# Raport z prac nad motywem Aftermarket

## Wykonane dzisiaj zadania:
1. Usunięto błędy krytyczne PHP (Fatal Error) związane z sesjami na hostingu Platne.pl (funkcja session_start() wywoływała awarię serwera).
2. Zabezpieczono wszystkie dynamiczne zapytania get_page_by_path w funkcjach i plikach motywu, zapobiegając błędom na nowych bazach danych.
3. Wdrożono dynamiczne wersjonowanie stylów i skryptów (token czasowy ver=time()), co omija buforowanie (cache) przeglądarek i serwera.
4. Zdiagnozowano problem z ucinaniem kodu kasy WooCommerce (spowodowany zbyt małym limitem pamięci PHP na serwerze i gigantyczną bazą krajów/regionów wysyłanych przez wbudowany skrypt).

## Jak uruchomić kasę WooCommerce (Krok po kroku):
Serwer Platne.pl ma zbyt małe zasoby, by przetworzyć domyślną listę krajów świata wysyłaną przez skrypt WooCommerce, co powoduje ucięcie strony w sekcji wp_head(). Aby kasa się załadowała:
1. Zaloguj się do kokpitu WordPressa (/wp-admin/).
2. Przejdź do WooCommerce -> Ustawienia -> Ogólne.
3. W polu "Lokalizacja sprzedaży" ustaw: Sprzedawaj tylko do wybranych krajów -> Polska.
4. W polu "Lokalizacje wysyłki" ustaw: Wysyłaj tylko do wybranych krajów -> Polska.
5. Zapisz zmiany. Kod kasy odciąży się o 90% i kasa natychmiast się wyświetli.
