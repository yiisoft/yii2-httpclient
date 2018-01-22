Opcje żądania
=============

Do celu modyfikacji procedury wykonania poszczególnego żądania służy [[\yii\httpclient\Request::$options]].
Dostępne są poniższe opcje:
 - timeout: integer, maksymalna liczba sekund przez którą żądanie może się wykonywać.
 - proxy: string, URI określający adres serwera proxy (np. tcp://proxy.example.com:5100).
 - userAgent: string, wartość nagłówka "User-Agent: ", który zostanie użyty w żądaniu HTTP.
 - followLocation: boolean, flaga określająca, czy należy przekierować lokację wg wskazania nagłówka "Location: ", który jest wysyłany przez serwer.
 - maxRedirects: integer, maksymalna liczba śledzonych przekierowań.
 - sslVerifyPeer: boolean, flaga określająca, czy powinna zostać przeprowadzona weryfikacja certyfikatu serwera.
 - sslCafile: string, lokalizacja pliku urzędu certyfikacji w lokalnym systemie plików, która powinna być skonfigurowana 
   w parze z opcją 'sslVerifyPeer' do uwierzytelnienia tożsamości zdalnego serwera.
 - sslCapath: string, folder przechowujący wiele certyfikatów CA.

Dla przykładu:

```php
use yii\httpclient\Client;

$client = new Client();

$response = $client->createRequest()
    ->setMethod('POST')
    ->setUrl('http://domain.com/api/1.0/users')
    ->setData(['name' => 'John Doe', 'email' => 'johndoe@domain.com'])
    ->setOptions([
        'proxy' => 'tcp://proxy.example.com:5100', // użyj proxy
        'timeout' => 5, // ustaw timeout na 5 sekund, gdyby serwer nie odpowiadał
    ])
    ->send();
```

> Tip: można ustawić domyślne opcje żądania za pomocą [[\yii\httpclient\Client::$requestConfig]]. W takim wypadku, aby
  dodać kolejne opcje zachowując parametry domyślnych należy skorzystać z metody [[\yii\httpclient\Request::addOptions()]].

Można również przekazać opcje odnoszące się do konkretnej metody przesyłania danych. Zwykle dotyczy to przypadku 
korzystania z [[\yii\httpclient\CurlTransport]], np. aby określić oddzielną wartość timeout dla połączenia i odbierania 
danych, dostarczaną w bibliotece PHP cURL. Można to zrobić w następujący sposób:

```php
use yii\httpclient\Client;

$client = new Client([
    'transport' => 'yii\httpclient\CurlTransport' // tylko cURL dostarcza wymaganą przez nas opcję
]);

$response = $client->createRequest()
    ->setMethod('POST')
    ->setUrl('http://domain.com/api/1.0/users')
    ->setData(['name' => 'John Doe', 'email' => 'johndoe@domain.com'])
    ->setOptions([
        CURLOPT_CONNECTTIMEOUT => 5, // timeout połączenia
        CURLOPT_TIMEOUT => 10, // timeout pobierania danych
    ])
    ->send();
```

Informacje na temat możliwych opcji metod przesyłania danych można znaleść w ich dokumentacji.
