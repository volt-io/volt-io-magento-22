# Volt: Zapłać przez bank -  Magento 2 Moduł płatności

## Krótki opis
Zaoferuj kupującym szybszy i bezpieczniejszy sposób płatności. Dzięki Volt klienci mogą dokonywać płatności bezpośrednio z aplikacji bankowej. Nie jest wymagana karta.

Jak to działa? Po prostu wybierają opcję "Zapłać przez bank" w koszyku, wybierają swój bank i potwierdź szczegóły płatności w swojej bankowości internetowej. "Volt: Zapłać przez bank" pojawi się jako opcja obok metod płatności, które już akceptujesz.

Ponieważ płatności są dokonywane z konta na konto, otrzymasz środki w czasie rzeczywistym i za ułamek kosztów płatności kartą - jednocześnie zapewniając swoim klientom sprawniejszą obsługę płatności.

Sprawdź działanie "Volt: Zapłać przez bank" w akcji [tutaj](https://www.volt.io/demos/checkout/).

## Podstawowe funkcjonalności
- Inicjowanie płatności między kontami w całej Europie
- Rozliczenia w czasie rzeczywistym
- Inteligentne śledzenie cyklu życia płatności
- Płatności mobilne automatycznie przekierowują do aplikacji bankowej klienta
- Płatności stacjonarne za pomocą kodu QR "Skanuj, aby zapłacić"

#### Ograniczenia
- Moduł nie jest dostępny dla wysyłki na wiele adresów (multishipping).

### Wymagania
- Wersja Magento: 2.3.0 - 2.4.6.
- Wersja PHP zgodna z wymaganiami Magento.

### [Lista zmian](CHANGELOG.md)

## Instalacja
1. Wykonaj poniższą komendę w katalogu głównym Magento 2:
```shell
composer require volt-io/volt-io-magento
```
2. Wykonaj poniższe komendy w celu włączenia modułu:
```shell
bin/magento module:enable Volt_Payment
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
bin/magento cache:flush
```

## Generowanie danych API
1. Zaloguj się do [Volt Fuzebox](https://fuzebox.volt.io).
2. Przejdź do **Configuration** -> **Applications**.
3. Kliknij **Create Application**.
4. Podaj **Application Name** i wybierz **Customer**, który ma być używany dla sklepu.
5. W polach **Payment return URLs** podaj następujący adres dla wszystkich statusów (zamień `your-store-url.com` na adres swojego sklepu):
```
https://your-store-url.com/volt/payment/back
```
6. W polach **Verify return URLs** podaj następujący adres dla wszystkich statusów (zamień `your-store-url.com` na adres swojego sklepu):
```
https://your-store-url.com/volt/payment/verify
```
7. Kliknij **Save**.
8. Skopiuj **Client ID** oraz **Client Secret** z sekcji **Credentials**.
9. Przejdź do zakładki **Payment Notification**.
10. Kliknij przycisk **Configure**.
11. Podaj następujący URL dla **Webhook URL** (zamień `your-store-url.com` na adres swojego sklepu):
```
https://your-store-url.com/volt/payment/notification
```
12. Podaj swój adres e-mail w polu **Failing notifications alert e-mail**.
13. Kliknij **Save**.

## Uzyskaj klucz powiadomień
1. Zaloguj się do [Volt Fuzebox](https://fuzebox.volt.io).
2. Przejdź do **Configuration** -> **Applications**.
3. Wybierz aplikację, dla której chcesz uzyskać tajny klucz powiadomień.
4. Przejdź do zakładki **Payment Notification**.
5. W sekcji **Notifications** kliknij przycisk **Pokaż** (ikona oka).
6. Skopiuj klucz powiadomień.

## Konfiguracja modułu
1. Zaloguj się do panelu administracyjnego Magento.
2. Przejdź do **Sklepy (Stores)** > **Konfiguracja (Configuration)**.
3. W lewym panelu, przejdź do **Sprzedaż (Sales)** > **Metody płatności (Payment Methods)**.
4. Rozwiń sekcję **Volt**.

### Ustawienia ogólne
1. Przejdź do [Konfiguracji modułu](#konfiguracja-modułu).
2. Ustaw **Włączony (Enabled)** na **Tak (Yes)**.
3. Ustaw **Tytuł (Title)** na nazwę płatności, która będzie wyświetlana w sklepie.
4. Ustaw **Sandbox** na **Tak (Yes)** jeśli chcesz używać środowiska testowego.
5. Ustaw **ID klienta (Client ID)** oraz **Klucz klienta (Client Secret)** na wartości, które otrzymałeś w [Generowaniu danych API](#generowanie-danych-api).
6. Ustaw **Klucz powiadomień (Notification Secret)** na klucz, który otrzymałeś w [Uzyskaj klucz powiadomień](#uzyskaj-klucz-powiadomień).
6. Ustaw **Nazwa użytkownika (Username)** oraz **Hasło użytkownika (Password)** użytkownika, którego wykorzystujesz do logowania się do konta [Volt Fuzebox](https://fuzebox.volt.io).
7. Ustaw **Kolejność sortowania (Sort order)** dla metody płatności.
8. Możesz zmienić **Status dla oczekującej płatności (Status for pending payment)** na status, który chcesz ustawić dla zamówienia po rozpoczęciu płatności.
9. Możesz zmienić **Status dla zakończonej płatności (Status for received payment)** na status, który chcesz ustawić dla zamówienia po zakończeniu płatności.
10. Możesz zmienić **Status dla nieudanej płatności (Status for failed payment)** na status, który chcesz ustawić dla zamówienia, jeśli płatność nie powiedzie się.
11. Kliknij **Zapisz (Save Config)** na górze strony.

## Zwroty

W celu włączenia zwrotów online, musisz mieć aktywną usługę [Volt Connect](https://www.volt.io/connect/).

1. Przejdź do [Konfiguracji modułu](#konfiguracja-modułu).
2. Ustaw **Zwroty włączone (Refund Enabled)** na **Tak (Yes)**.
3. Kliknij **Zapisz (Save Config)** na górze strony.

## Wsparcie
Jeśli masz jakiekolwiek problemy z tym modułem, otwórz nowy Issue na [GitHub](https://github.com/volt-io/volt-io-magento/issues).

