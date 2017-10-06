Пакетная отправка запросов
=====================

HTTP-клиент позволяет отправлять несколько запросов одновременно, используя метод [[\yii\httpclient\Client::batchSend()]]:

```php
use yii\httpclient\Client;

$client = new Client();

$requests = [
    $client->get('http://domain.com/keep-alive'),
    $client->post('http://domain.com/notify', ['userId' => 12]),
];
$responses = $client->batchSend($requests);
```

Особый [транспорт](usage-transports.md) может дать пользу, при использовании данного подхода, позволяя повысить производительность. 
Среди встроенных транспортов только [[\yii\httpclient\CurlTransport]] делает это. Он позволяет отправлять запросы параллельно, 
что экономит время выполнения программы.

> Note: только некоторые конкретные транспорты позволяют особым образом обрабатывать запросы, посредством `batchSend()` 
  что даёт некоторую выгоду. По умолчанию транспорт отправляет их по одному без каких-либо ошибок и предупреждений. Убедитесь, 
  что вы настроили правильный транспорт для клиента, если хотите добиться повышения производительности.

Метод `batchSend()` возвращает массив ответов, ключи которых соответствуют ключам из массива запросов.
Это позволяет с лёгкостью обрабатывать конкретный ответ на запрос:

```php
use yii\httpclient\Client;

$client = new Client();

$requests = [
    'news' => $client->get('http://domain.com/news'),
    'friends' => $client->get('http://domain.com/user/friends', ['userId' => 12]),
    'newComment' => $client->post('http://domain.com/user/comments', ['userId' => 12, 'content' => 'New comment']),
];
$responses = $client->batchSend($requests);

// result of `GET http://domain.com/news` :
if ($responses['news']->isOk) {
    echo $responses['news']->content;
}

// result of `GET http://domain.com/user/friends` :
if ($responses['friends']->isOk) {
    echo $responses['friends']->content;
}

// result of `POST http://domain.com/user/comments` :
if ($responses['newComment']->isOk) {
    echo "Comment has been added successfully";
}
```
