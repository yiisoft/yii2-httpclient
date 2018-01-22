Zdarzenia
=========

[[\yii\httpclient\Request]] dostarcza kilku zdarzeń, które można obsłużyć za pomocą procedur lub behawiorów:

- [[\yii\httpclient\Request::EVENT_BEFORE_SEND]] - wywołane przed wysłaniem żądania,
- [[\yii\httpclient\Request::EVENT_AFTER_SEND]] - wywołane po wysłaniu żądania.

Powyższe zdarzenia mogą być użyte do zmodyfikowania parametrów żądania lub otrzymanej odpowiedzi.
Przykładowo:

```php
use yii\httpclient\Client;
use yii\httpclient\Request;
use yii\httpclient\RequestEvent;

$client = new Client();

$request = $client->createRequest()
    ->setMethod('GET')
    ->setUrl('http://api.domain.com')
    ->setData(['param' => 'value']);

// Zapewnij wygenerowanie odpowiedniej sygnatury na podstawie zestawu danych:
$request->on(Request::EVENT_BEFORE_SEND, function (RequestEvent $event) {
    $data = $event->request->getData();

    $signature = md5(http_build_query($data));
    $data['signature'] = $signature;

    $event->request->setData($data);
});

// Normalizuj dane odpowiedzi:
$request->on(Request::EVENT_AFTER_SEND, function (RequestEvent $event) {
    $data = $event->response->getData();

    $data['content'] = base64_decode($data['encoded_content']);

    $event->response->setData($data);
});

$response = $request->send();
```

Dołączanie procedur obsługi zdarzeń do instancji [[\yii\httpclient\Request]] nie jest zbyt praktyczne.
Zamiast tego można jednak obsłużyć służące identycznym celom zdarzenia klasy [[\yii\httpclient\Client]]:

- [[\yii\httpclient\Client::EVENT_BEFORE_SEND]] - wywołane przed wysłaniem żądania,
- [[\yii\httpclient\Client::EVENT_AFTER_SEND]] - wywołane po wysłaniu żądania.

Powyższe zdarzenia wywoływane są dla wszystkich żądań przesłanych przez klienta w identyczny sposób i z taką samą 
sygnaturą jak te w [[\yii\httpclient\Request]].
Dla przykładu:

```php
use yii\httpclient\Client;
use yii\httpclient\RequestEvent;

$client = new Client();

$client->on(Client::EVENT_BEFORE_SEND, function (RequestEvent $event) {
    // ...
});
$client->on(Client::EVENT_AFTER_SEND, function (RequestEvent $event) {
    // ...
});
```

> Note: [[\yii\httpclient\Client]] i [[\yii\httpclient\Request]] używają tych samych nazw dla zdarzeń 
  `EVENT_BEFORE_SEND` i `EVENT_AFTER_SEND`, dzięki czemu można stworzyć behawior, który może być użyty dla obu tych klas.
