Transports
==========

[[\yii\httpclient\Client]] provides several different ways to actually send an HTTP message - several transports.
Predefined transports are:

 - [[\yii\httpclient\StreamTransport]] - sends HTTP messages using [Streams](http://php.net/manual/en/book.stream.php).
   This transport is used by default. It does not require any additional PHP extensions or libraries installed,
   but does not support advanced features like batch sending.
 - [[\yii\httpclient\CurlTransport]] - sends HTTP messages using [Client URL Library (cURL)](http://php.net/manual/en/book.curl.php)
   This transport requires PHP 'curl' extension to be installed, but provides support for advanced features, like
   batch sending.

You may configure the transport to be used by particular client using [[\yii\httpclient\Client::transport]]:

```php
use yii\httpclient\Client;

$client = new Client([
    'transport' => 'yii\httpclient\CurlTransport'
]);
```


## Creating custom transport

You may create your own transport, which will perform message sending in its own way. To do so you should
extend [[\yii\httpclient\Transport]] class and implement at least `send()` method. All you need to do is
determine HTTP response content and headers, then you can compose a response object from them using
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

You may as well override `batchSend()` method if there is a way to send multiple requests with better performance
like sending them asynchronously in parallel.
