Базовое использование
===========

Для отправки HTTP запросов, вам необходимо создать экземпляр [[\yii\httpclient\Client]] и использовать его метод
`createRequest()` для создания нового HTTP запроса. Затем Вы должны настроить все параметры запроса в соответствии с вашими требованиями и отправить запрос. 
В качестве результата, Вы получите экземпляр [[\yii\httpclient\Response]],
который содержит полную информацию об ответе и его содержимое.
Например:

```php
use yii\httpclient\Client;

$client = new Client();
$response = $client->createRequest()
    ->setMethod('post')
    ->setUrl('http://example.com/api/1.0/users')
    ->setData(['name' => 'John Doe', 'email' => 'johndoe@example.com'])
    ->send();
if ($response->isOk) {
    $newUserId = $response->data['id'];
}
```

Так же Вы можете использовать методы `get()`, `post()`, `put()` и т.п. для упрощения подготовки нового запроса.
Если Вы используете один экземпляр [[\yii\httpclient\Client]] для множества запросов на один и тот же домен (
например в случае использования REST API), Вы можете передать имя такого домена в свойство `baseUrl`. Это позволит 
, при создании нового запроса, указывать только относительный URL-адрес.
В итоге, несколько запросов к некоему REST API могут выглядеть следующим образом:

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'http://example.com/api/1.0']);

$newUserResponse = $client->post('users', ['name' => 'John Doe', 'email' => 'johndoe@example.com'])->send();
$articleResponse = $client->get('articles', ['name' => 'Yii 2.0'])->send();
$client->post('subscriptions', ['user_id' => $newUserResponse->data['id'], 'article_id' => $articleResponse->data['id']])->send();
```


## Использование различных форматов содержимого

По умолчанию данные HTTP-запроса отправляются в виде 'form-urlencoded', т.е. `param1=value1&param2=value2`.
Это общий формат для веб-форм, но не для REST API, который обычно требует, что бы содержимое 
было в формате JSON или XML. Вы можете настроить формат, используемый для содержимого запроса, используя свойство `format`
или метод `setFormat()`.
Поддерживаются следующие форматы:

 - [[\yii\httpclient\Client::FORMAT_JSON]] - формат JSON
 - [[\yii\httpclient\Client::FORMAT_URLENCODED]] - *urlencoded строка запроса по RFC1738*
 - [[\yii\httpclient\Client::FORMAT_RAW_URLENCODED]] - *urlencoded строка запроса по PHP_QUERY_RFC3986*
 - [[\yii\httpclient\Client::FORMAT_XML]] - формат XML

Например:

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'http://example.com/api/1.0']);
$response = $client->createRequest()
    ->setFormat(Client::FORMAT_JSON)
    ->setUrl('articles/search')
    ->setData([
        'query_string' => 'Yii',
        'filter' => [
            'date' => ['>' => '2015-08-01']
        ],
    ])
    ->send();
```

Объект ответа автоматически определяет формат содержимого на основании заголовка 'Content-Type' и самого содержимого.
Поэтому в большинстве случаев Вам не нужно указывать формат ответа, Вы можете его обработать используя метод `getData()`
или свойство `data`. Далее, используя приведенный выше пример, мы можем получить данные ответа следующим образом:

```php
$responseData = $response->getData(); // получаем все статьи
count($response->data) // кол-во статей
$article = $response->data[0] // получаем первую статью
```


## Работа с исходным содержимым

Никто не заставляет Вас использовать встроенные форматы. Вы можете указать исходное содержимое для вашего HTTP-запроса, 
а так же обработать исходное содержимое ответа. Например:

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'http://example.com/api/1.0']);
$response = $client->createRequest()
    ->setUrl('articles/search')
    ->addHeaders(['content-type' => 'application/json'])
    ->setContent('{query_string: "Yii"}')
    ->send();

echo 'Результаты поиска:<br>';
echo $response->content;
```

[[\yii\httpclient\Request]] форматирует `data` только если `content` не задан.
[[\yii\httpclient\Response]] обрабатывает `content` только если `data` было запрошено.


## Предварительная настройка объектов запроса и ответа

Если Вы используете один экземпляр [[\yii\httpclient\Client]] для нескольких похожих запросов,
например, когда работаете с REST API, Вы можете упростить и ускорить ваш код, указав
свою собственную конфигурацию для объектов запроса и ответа. Это можно сделать через поля `requestConfig`
и `responseConfig` в [[\yii\httpclient\Client]].
Например: Вы захотели указать формат JSON для всего запроса, созданного конкретным клиентом:

```php
use yii\httpclient\Client;

$client = new Client([
    'baseUrl' => 'http://example.com/api/1.0',
    'requestConfig' => [
        'format' => Client::FORMAT_JSON
    ],
    'responseConfig' => [
        'format' => Client::FORMAT_JSON
    ],
]);

$request = $client->createRequest();
echo $request->format; // outputs: 'json'
```

> Tip: так же Вы можете указать собственные классы объектов запроса и ответа
  *для облегчения добавления некоторой, необходимой Вам, функциональности*, используя ключ 'class' в конфигурационном массиве.


## Работа с заголовками

Вы можете указать заголовки запросов, используя методы `setHeaders()` и `addHeaders()`.
Вы можете использовать метод `getHeaders()` или свойство `headers` для получения уже определенных заголовков в качестве экземпляра
[[\yii\web\HeaderCollection]]. Например:

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'http://example.com/api/1.0']);
$request = $client->createRequest()
    ->setHeaders(['content-type' => 'application/json'])
    ->addHeaders(['user-agent' => 'My User Agent']);

$request->getHeaders()->add('accept-language', 'en-US;en');
$request->headers->set('user-agent', 'User agent override');
```

Когда у Вас есть объект ответа, Вы можете получить доступ ко всем заголовкам ответов используя метод `getHeaders()` 
или свойство `headers`:

```php
$response = $request->send();
echo $response->getHeaders()->get('content-type');
echo $response->headers->get('content-encoding');
```


## Работа с *Куки-файлами*

Хотя *Куки-файлы* и передаются, как значения заголовков, [[\yii\httpclient\Request]] и [[\yii\httpclient\Request]]
предоставляют отдельный интерфейс для работы с ними при помощи [[\yii\web\Cookie]] and [[\yii\web\CookieCollection]].

Вы можете указать *Куки-файлы* запроса используя методы `setCookies()` или `addCookies()`.
Вы можете использовать метод `getCookies()` или свойство `cookies` для получения уже определенных *Куки-файлов* 
в виде экземпляра [[\yii\web\CookieCollection]]. Например:

```php
use yii\httpclient\Client;
use yii\web\Cookie;

$client = new Client(['baseUrl' => 'http://example.com/api/1.0']);
$request = $client->createRequest()
    ->setCookies([
        ['name' => 'country', 'value' => 'USA'],
        new Cookie(['name' => 'language', 'value' => 'en-US']),
    ])
    ->addCookies([
        ['name' => 'view_mode', 'value' => 'full']
    ]);

$request->cookies->add(['name' => 'display-notification', 'value' => '0']);
```

После того, как у Вас есть объект ответа, Вы можете получить доступ ко всем *Куки-файлам* ответов, используя метод `getCookies()` 
или свойство `cookies`:

```php
$response = $request->send();
echo $response->getCookies()->get('country');
echo $response->headers->get('PHPSESSID');
```

Вы можете передать *Куки-файлы* из объекта ответа для запроса, *используя простое копирование*.
Например: предположим, что нам нужно изменить профиль пользователя в каком-либо веб-приложении, которое доступно 
только после входа в систему, поэтому сначала нам нужно войти в систему, после чего использовать созданную сессию для дальнейших действий:

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'http://example.com']);

$loginResponse = $client->post('login', [
    'username' => 'johndoe',
    'password' => 'somepassword',
])->send();

// $loginResponse->cookies->get('PHPSESSID') - содержит новый идентификатор сессии

$client->post('account/profile', ['birthDate' => '10/11/1982'])
    ->setCookies($loginResponse->cookies) // передача *Куки-файла* из ответа в запрос
    ->send();
```
