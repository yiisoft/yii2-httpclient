Przesyłanie danych
==================

[[\yii\httpclient\Client]] dostarcza kilku różnych metod do przesyłania wiadomości HTTP - transportów.
Predefiniowane transporty to:

 - [[\yii\httpclient\StreamTransport]] - wysyła wiadomości HTTP za pomocą [Streams](http://php.net/manual/pl/book.stream.php).
   Ten transport jest używany domyślnie. Nie wymaga on instalowania dodatkowych rozszerzeń lub bibliotek PHP, ale nie 
   wspiera zaawansowanych funkcjonalności jak wysyłanie serii żądań.
 - [[\yii\httpclient\CurlTransport]] - wysyła wiadomości HTTP za pomocą [Client URL Library (cURL)](http://php.net/manual/pl/book.curl.php)
   Ten transport wymaga zainstalowanego rozszerzenia PHP 'curl', ale zapewnia zaawansowane funkcjonalności jak wysyłanie serii żądań.

Można skonfigurować transport używany przez poszczególnego klienta za pomocą [[\yii\httpclient\Client::$transport]]:

```php
use yii\httpclient\Client;

$client = new Client([
    'transport' => 'yii\httpclient\CurlTransport'
]);
```


## Tworzenie własnego transportu

Można stworzyć własny typ transportu, który będzie przesyłał dane w określony sposób. Aby tego dokonać, należy rozszerzyć 
klasę [[\yii\httpclient\Transport]] i zaimplementować w niej przynajmniej metodę `send()`. Jedyne co trzeba zrobić, to 
określić treść odpowiedzi HTTP i nagłówki, a następnie skomponować z nich obiekt odpowiedzi za pomocą 
[[\yii\httpclient\Client::createResponse()]]:

```php
use yii\httpclient\Transport;

class MyTransport extends Transport
{
    /**
     * @inheritdoc
     */
    public function send($request)
    {
        $responseContent = '???';
        $responseHeaders = ['???'];

        return $request->client->createResponse($responseContent, $responseHeaders);
    }
}
```

Można również przeciążyć metodę `batchSend()`, jeśli dostępny jest sposób na wysłanie wielu żądań z większą wydajnością, 
jak w przypadku wysyłania ich równolegle i asynchronicznie.
