批量 request 请求
=====================

可以使用 [[\yii\httpclient\Client::batchSend()]] 方法同时发送多个请求：

```php
use yii\httpclient\Client;

$client = new Client();

$requests = [
    $client->get('http://domain.com/keep-alive'),
    $client->post('http://domain.com/notify', ['userId' => 12]),
];
$responses = $client->batchSend($requests);
```

可使用特定的 [传输方式](usage-transports.md) 提升其性能。
在内置传输中，只有 [[\yii\httpclient\CurlTransport]] 允许并行发送请求，从而减少程序执行时间。

> 注意：只有某些特定的传输传输方式可以使用 `batchSend()` 并行发送请求。 默认情况下，请求只是一个接一个地发送，没有任何错误或警告抛出。如果需要提升性能 ，则需为客户端配置正确的传输方式。

`batchSend()` 方法返回响应数组，这些键对应于请求数组中的键。
可以使用如下方式简化响应的处理：

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
