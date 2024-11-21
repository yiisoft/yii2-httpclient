Transports
==========

[[\yii\httpclient\Client]] provides several different ways to actually send an HTTP message - several transports.
Predefined transports are:

 - [[\yii\httpclient\StreamTransport]] - sends HTTP messages using [Streams](https://php.net/manual/en/book.stream.php).
   This transport is used by default. It does not require any additional PHP extensions or libraries installed,
   but does not support advanced features like batch sending.
 - [[\yii\httpclient\CurlTransport]] - sends HTTP messages using [Client URL Library (cURL)](https://php.net/manual/en/book.curl.php)
   This transport requires PHP 'curl' extension to be installed, but provides support for advanced features, like
   batch sending.
 - [[\yii\httpclient\MockTransport]] - useful in test automation context. It does not send any real request and returns
   the responses it is instructed to return.

You may configure the transport to be used by particular client using [[\yii\httpclient\Client::$transport]]:

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

## Example usage for MockTransport

In order to mock [[\yii\httpclient\Client]] requests in your tests, you can use the [[\yii\httpclient\MockTransport]]
`transport` and craft your own responses using `appendResponse()`.

For example, if you have a component consuming an API like this:

```php
use yii\httpclient\Client;

/**
 * @property-read Client $client
 */
class ApiConsumer extends \yii\base\Component
{
    public $transport = 'yii\httpclient\StreamTransport';

    private $_client = null;

    public function getClient()
    {
        if (!$this->_client) {
            $this->_client = new Client(['transport' => $this->transport]);
        }

        return $this->_client;
    }

    public function getAuthToken()
    {
        $response = $this->client->createRequest()
          ->setUrl(["https://example.com/oauth/v2/token", "refresh_token" => "TEST_TOKEN", "client_id" => "CLIENT_ID", /* ... */])
          ->send();

        if (!isset($response->data->access_token)) {
          throw new \yii\base\Exception("Could not find access_token in API response.");
        }

        return $response->data->access_token;
    }
}
```

You can mock the HTTP exchange by swapping transport in your test:

```php
use yii\httpclient\Client;
use yii\httpclient\MockTransport;

class ApiConsumerTest extends \Codeception\Test\Unit
{
    public function testCanLogin()
    {
        // Override the transport
        $consumer = new ApiConsumer(['endpoint' => 'yii\httpclient\MockTransport']);

        // Mock the next response for the send() call
        $consumer->client->transport->appendResponse(
            $consumer->client->createResponse(json_encode([
                "access_token" => "1000.11112222333334444455555666666.aaaaaabbbbbbbccccccdddddddeeeeee",
                "api_domain" => "https://www.example.com",
                "token_type" => "Bearer",
                "expires_in" => 3600,
            ]), ["HTTP/1.1 200 OK"])
        );

        $token = $consumer->login();

        verify($token)->equals("1000.11112222333334444455555666666.aaaaaabbbbbbbccccccdddddddeeeeee");
    }
}
```
