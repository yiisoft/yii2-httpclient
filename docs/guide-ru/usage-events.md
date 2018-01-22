События
======

[[\yii\httpclient\Request]] предоставляет несколько событий, которые могут быть обрабатываться при помощи обработчика событий или поведения:

- [[\yii\httpclient\Request::EVENT_BEFORE_SEND]] - вызывается перед отправкой запроса.
- [[\yii\httpclient\Request::EVENT_AFTER_SEND]] - вызывается после отправки запроса.

Эти события могут использоваться для настройки параметров запроса или полученного ответа.
Например:

```php
use yii\httpclient\Client;
use yii\httpclient\Request;
use yii\httpclient\RequestEvent;

$client = new Client();

$request = $client->createRequest()
    ->setMethod('GET')
    ->setUrl('http://api.domain.com')
    ->setData(['param' => 'value']);

// Обеспечение генерации сигнатур на основе окончательного набора данных:
$request->on(Request::EVENT_BEFORE_SEND, function (RequestEvent $event) {
    $data = $event->request->getData();

    $signature = md5(http_build_query($data));
    $data['signature'] = $signature;

    $event->request->setData($data);
});

// Нормализация данных ответа:
$request->on(Request::EVENT_AFTER_SEND, function (RequestEvent $event) {
    $data = $event->response->getData();

    $data['content'] = base64_decode($data['encoded_content']);

    $event->response->setData($data);
});

$response = $request->send();
```

Привязывание обработчиков событий к экземпляру [[\yii\httpclient\Request]] не очень практично.
Вы можете обрабатывать всё те же случаи применения, используя события класса [[\yii\httpclient\Client]]:

- [[\yii\httpclient\Client::EVENT_BEFORE_SEND]] - вызывается перед отправкой запроса.
- [[\yii\httpclient\Client::EVENT_AFTER_SEND]] - вызывается после отправки запроса.

Эти события срабатывают для всех запросов, созданных клиентом, таким же образом и с той же сигнатурой, 
что и из [[\yii\httpclient\Request]].
Например:

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

> Note: [[\yii\httpclient\Client]] и [[\yii\httpclient\Request]] используют одинаковые имена для событий `EVENT_BEFORE_SEND` и
  `EVENT_AFTER_SEND`, поэтому вы можете создать поведение, которое может применяться для обоих этих классов.
