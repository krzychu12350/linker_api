TO DO:

1. Endpoint do zmiany nazwy grupy edycja
2. Preferencja tabelka z jakimi religami wiekami itd. ma wyswiela w niezmatchowanych
3. uzytkownicy z rola moderator z paginacja (wyszukiwanie nazawa koleumny i query), endpoint do dodania moderatora, usuniecie moderatora
4. endpoint do pobierania wszystkich zgloszen z paginacja, wyswietlenie pojedynczego zgloszenia (dane zglaszengo usera, pliki zgloszenia, tytul, opis),
endpoint do edycji zgloszenia gdzie mozna.
Obsluga edycji zgloszenia
1. Odrzucenia -zamiasna statusu na odrzucone
2. Zbanowanie uzytkownika na czas okreslony i permanentne zbanowanie (soft delete, nie da sie juz zrobic na ten mobile) - akutalizacja endpointu do rejestracji wykluczenie uzytkownik zbannowych na nieokreslony czas 

dodanie przy podjeciu decyzji ewent o wyniku zgloszenia (zgloszony uzytkownik zostal zbanowany, zgloszenie odrzucone)
tj. dodanie do bazy nowego powiadomienia i wysylnie przez pushera do fronetu przez triggerowania zdarzenia