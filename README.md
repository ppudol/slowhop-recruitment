# Zadanie rekrutacyjne 

# Kryteria akceptacji

Zbuduj prosty mikroserwis w oparciu o framework Symfony w wersji 4 lub wyżej. Zadaniem mikroserwisu ma być przetworzenie prostego pliku w formacie iCal, który dostępny jest pod adresem: https://slowhop.com/icalendar-export/api-v1/21c0ed902d012461d28605cdb2a8b7a2.ics. 

- Stwórz końcówkę REST, w ramach której pobierzesz plik iCal zwrócisz odpowiedź w formacie JSON lub XML (obojętnie), gdzie dla każdego eventu zwrócisz informacje: id, start, end i summary (przykład odpowiedzi: https://gist.github.com/pkalisz/daae919cb6c0714a116caedb37ca2315).
- Stwórz polecenie konsolowe (Command), które po odpaleniu zrobi dokładnie to samo co końcówka RESTowa z punktu 1 i wypluje wynik na ekran
- Zdockeryzuj ten mikroserwis, idealnie będzie jeśli będę mógł go odpalić przy pomocy “docker compose”
- Napisz jakiś prosty unit test
- Użyj PHP w wersji 7+
- Buduj serwis z myślą o utrzymaniu go na produkcji, pamiętaj o czytelnym kodzie, logach, etc..
- BONUS: Wynik działania końcówki z p1 i commanda z p2 zrzuć do pliku i zapisz ten plik w S3. Wymaga to założenia swojego konta w AWS, które do pewnego momentu jest darmowe, więc nie narażasz się w tym wypadku na żadne koszty.

# Odpalanie projektu

```zsh
docker compose build
docker compose up -d
```
**Uwaga**
u mnie był problem z uprawnieniami do folderów `var/log/`, `public/uploads` możliwe że będzie potrzebny chmod 

# instalacja zależności composera
```
docker exec slowhop-php-fpm-1 composer install
```

# Upload plików na S3
W `config/secrets/prod` należy wrzucić plik `prod.decrypt.private.php` który podesłałem na maila.

# Działanie
Pod adresem `http://localhost:8008/api/get-calendar-data` znajduje się wystawiona końcówka api.
Komendę uruchamiamy za pomacą `bin/console api:get-calendar-data`.
