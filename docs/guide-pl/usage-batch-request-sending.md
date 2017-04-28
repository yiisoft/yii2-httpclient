Batch request sending
=====================

HTTP Client allows sending multiple requests at once using [[\yii\httpclient\Client::batchSend()]] method:

```php
use yii\httpclient\Client;

$client = new Client();

$requests = [
    $client->get('http://domain.com/keep-alive'),
    $client->post('http://domain.com/notify', ['userId' => 12]),
];
$responses = $client->batchSend($requests);
```

Particular [transport](usage-transports.md) may benefit from this method usage, allowing to increase the performance.
Among the built-in transports, only [[\yii\httpclient\CurlTransport]] does this. It allows to send requests in parallel,
which saves the program execution time.

> Note: only some particular transports allows processing requests at `batchSend()` in special way, which provides some
  benefit. By default transport just sends them one by one without any error or warning thrown. Make sure you have
  configured correct transport for the client, if you wish to achieve performance boost.

`batchSend()` method returns the array of the responses, which keys correspond the ones from array of requests.
This allows you to process particular request response in easy way:

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
