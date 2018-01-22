Request 参数
===============

使用 [[\yii\httpclient\Request::$options]] 来调整特定的请求。
支持以下参数：

 - timeout: integer, 允许执行请求的最大超时时间（秒）。
 - proxy: string, 指定代理服务器的地址的 URI。(例如 tcp://proxy.example.com:5100)
 - userAgent: string, 在 HTTP 请求中使用的 “User-Agent：” 标头的内容。
 - followLocation: boolean, 是否重定向至 HTTP 头信息中的任意 “Location：”。
 - maxRedirects: integer, 要跟踪的重定向的最大数量。
 - sslVerifyPeer: boolean, 是否启用 ssl 验证。
 - sslCafile: string, CA 证书在本地文件系统上的位置，应与 'sslVerifyPeer' 参数一起使用以验证服务端的身份。
 - sslCapath: string, 一个保存多个CA证书的目录。

例如:

```php
use yii\httpclient\Client;

$client = new Client();

$response = $client->createRequest()
    ->setMethod('POST')
    ->setUrl('http://domain.com/api/1.0/users')
    ->setData(['name' => 'John Doe', 'email' => 'johndoe@domain.com'])
    ->setOptions([
        'proxy' => 'tcp://proxy.example.com:5100', // use a Proxy
        'timeout' => 5, // set timeout to 5 seconds for the case server is not responding
    ])
    ->send();
```

> 提示：可以通过 [[\yii\httpclient\Client::$requestConfig]] 对默认请求进行设置。 可以使用 [[\yii\httpclient\Request::addOptions()]] 在添加其他设置的同时保留原始的其他参数。

在使用 [[\yii\httpclient\CurlTransport]] 时，可配置特殊的请求参数。 例如：指定 cUrl 请求的连接和接收数据的超时时间：

```php
use yii\httpclient\Client;

$client = new Client([
    'transport' => 'yii\httpclient\CurlTransport' // only cURL supports the options we need
]);

$response = $client->createRequest()
    ->setMethod('POST')
    ->setUrl('http://domain.com/api/1.0/users')
    ->setData(['name' => 'John Doe', 'email' => 'johndoe@domain.com'])
    ->setOptions([
        CURLOPT_CONNECTTIMEOUT => 5, // connection timeout
        CURLOPT_TIMEOUT => 10, // data receiving timeout
    ])
    ->send();
```

有关特定参数支持的详细信息，请参阅特定的传输类文档。