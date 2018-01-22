事件
======

[[\yii\httpclient\Request]] 提供了一些事件，可以通过事件处理程序（ event handlers ）或行为进行捕获并处理：

- [[\yii\httpclient\Request::EVENT_BEFORE_SEND]] - 在发送请求前抛出。
- [[\yii\httpclient\Request::EVENT_AFTER_SEND]] - 在发送请求后抛出。

这些事件可以用于调整请求参数或响应的接收。
例如：

```php
use yii\httpclient\Client;
use yii\httpclient\Request;
use yii\httpclient\RequestEvent;

$client = new Client();

$request = $client->createRequest()
    ->setMethod('GET')
    ->setUrl('http://api.domain.com')
    ->setData(['param' => 'value']);

// Ensure signature generation based on final data set:
$request->on(Request::EVENT_BEFORE_SEND, function (RequestEvent $event) {
    $data = $event->request->getData();

    $signature = md5(http_build_query($data));
    $data['signature'] = $signature;

    $event->request->setData($data);
});

// Normalize response data:
$request->on(Request::EVENT_AFTER_SEND, function (RequestEvent $event) {
    $data = $event->response->getData();

    $data['content'] = base64_decode($data['encoded_content']);

    $event->response->setData($data);
});

$response = $request->send();
```

将事件处理程序（ event handlers ）绑定到 [[\yii\httpclient\Request]] 实例的做法并不是很好。
可以使用 [[\yii\httpclient\Client]] 类的事件处理相同的事件：

- [[\yii\httpclient\Client::EVENT_BEFORE_SEND]] - 在发送请求前抛出.
- [[\yii\httpclient\Client::EVENT_AFTER_SEND]] - 在发送请求后抛出.

以相同方式创建的所有请求，以及 [[\yii\httpclient\Request]] 中相同签名的请求，都会触发以下事件。
例如：
These events are triggered for all requests created via client in the same way and with the same signature as the ones from [[\yii\httpclient\Request]].
For example:

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

> 注意： [[\yii\httpclient\Client]] 和 [[\yii\httpclient\Request]] 使用了相同的事件名称，即： `EVENT_BEFORE_SEND` 和 `EVENT_AFTER_SEND` ，因此创 jain的行为可被同时应用在这两个类上。
