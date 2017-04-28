Wysyłanie serii żądań
=====================

Klient HTTP pozwala na wysłanie wielu żądań jednocześnie za pomocą metody [[\yii\httpclient\Client::batchSend()]]:

```php
use yii\httpclient\Client;

$client = new Client();

$requests = [
    $client->get('http://domain.com/keep-alive'),
    $client->post('http://domain.com/notify', ['userId' => 12]),
];
$responses = $client->batchSend($requests);
```

Metoda ta pozwala na zwiększenie wydajności w kontekście [przesyłania danych](usage-transports.md).
Spośród wbudowanych metod przesyłania jedynie [[\yii\httpclient\CurlTransport]] jest przystosowany do jej wykorzystania. 
Za pomocą powyższej metody możliwe jest wysłanie żądań równolegle, dzięki czemu można zmniejszyć łączny czas pracy 
programu.

> Note: nie każda metoda pzesyłania danych pozwala na przetwarzanie żądań wysłanych za pomocą `batchSend()` w specjalny 
  sposób, który mógłby zapewnić dodatkowe korzyści. Domyślnie są one wysyłane jedno za drugim, bez zwracania błędów lub 
  wyrzucania wyjątków. Należy upewnić się, czy klient jest skonfigurowany z odpowiednią metodą transportu, aby uzyskać 
  wzrost wydajności.

Metoda `batchSend()` zwraca tablicę odpowiedzi, której klucze odpowiadają tym użytym w tablicy żądań.
Dzięki temu można przetworzyć odpowiednie żądanie w prosty sposób:

```php
use yii\httpclient\Client;

$client = new Client();

$requests = [
    'news' => $client->get('http://domain.com/news'),
    'friends' => $client->get('http://domain.com/user/friends', ['userId' => 12]),
    'newComment' => $client->post('http://domain.com/user/comments', ['userId' => 12, 'content' => 'Nowy komentarz']),
];
$responses = $client->batchSend($requests);

// rezultat `GET http://domain.com/news` :
if ($responses['news']->isOk) {
    echo $responses['news']->content;
}

// rezultat `GET http://domain.com/user/friends` :
if ($responses['friends']->isOk) {
    echo $responses['friends']->content;
}

// rezultat `POST http://domain.com/user/comments` :
if ($responses['newComment']->isOk) {
    echo "Komentarz został dodany";
}
```
