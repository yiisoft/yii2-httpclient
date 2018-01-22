数据传输
==========

[[\yii\httpclient\Client]] 提供了几种不同的方式来发送HTTP消息。
预定义的传输方式是：

 - [[\yii\httpclient\StreamTransport]] - 使用 [Streams](http://php.net/manual/en/book.stream.php) 发送HTTP消息。默认情况下使用此传输。 它不需要安装任何额外的PHP扩展或库，但不支持高级功能，如批量请求。
 - [[\yii\httpclient\CurlTransport]] - 使用 [Client URL Library (cURL)](http://php.net/manual/en/book.curl.php) 发送HTTP消息，此传输需要安装 PHP 'curl' 扩展，但支持高级功能， 例如批量请求。

可以使用 [[\yii\httpclient\Client::$transport]] 配置客户端使用的传输方式：

```php
use yii\httpclient\Client;

$client = new Client([
    'transport' => 'yii\httpclient\CurlTransport'
]);
```


## 创建自定义传输方式

通过继承 [[\yii\httpclient\Transport]] 类，并实现 `send()` 方法。 然后设置响应的内容和 HTTP 头信息，最后返回 [[\yii\httpclient\Client::createResponse()]] 对象即可：

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

如果有更高性能的发送多个请求的方法，比如并行异步发送它们，也可以重写 `batchSend()` 方法。
