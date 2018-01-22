基本使用
===========

为了发送 HTTP 请求，首先实例化 [[\yii\httpclient\Client]] 类，然后调用其中的 `createRequest()` 方法即可创建新的HTTP请求。然后配置请求中所必须的参数，并发送请求。返回值是一个基于 [[\yii\httpclient\Response]] 对象的实例，该实例内包含所有的相应信息和数据。

例如:

```php
use yii\httpclient\Client;

$client = new Client();
$response = $client->createRequest()
    ->setMethod('POST')
    ->setUrl('http://example.com/api/1.0/users')
    ->setData(['name' => 'John Doe', 'email' => 'johndoe@example.com'])
    ->send();
if ($response->isOk) {
    $newUserId = $response->data['id'];
}
```

可以使用 `get()`, `post()`, `put()` 等方法来简化新的请求。
如果使用单一的 [[\yii\httpclient\Client]] 实例对同一域发起多个请求（例如在使用 REST API 的情况下），可以设置此域的 `baseUrl` 属性。 这样在创建新请求时只指定相对的 URL 即可。
因此，对某些 REST API 的多个请求可能如下代码所示:

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'http://example.com/api/1.0']);

$newUserResponse = $client->post('users', ['name' => 'John Doe', 'email' => 'johndoe@example.com'])->send();
$articleResponse = $client->get('articles', ['name' => 'Yii 2.0'])->send();
$client->post('subscriptions', ['user_id' => $newUserResponse->data['id'], 'article_id' => $articleResponse->data['id']])->send();
```


## 使用不同格式的内容

默认情况下， HTTP 请求数据以 “form-urlencoded” 形式发送，例如： `param1=value1&param2=value2`.
这是 Web 表单的通用格式，但不是 REST API 的通用格式，通常要求内容应为JSON或XML格式。 可以使用 `format` 属性或通过 `setFormat()` 方法设置用于请求内容的格式。
以下是支持的格式:

 - [[\yii\httpclient\Client::FORMAT_JSON]] - JSON format
 - [[\yii\httpclient\Client::FORMAT_URLENCODED]] - urlencoded by RFC1738 query string
 - [[\yii\httpclient\Client::FORMAT_RAW_URLENCODED]] - urlencoded by PHP_QUERY_RFC3986 query string
 - [[\yii\httpclient\Client::FORMAT_XML]] - XML format

例如:

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

response 对象基于 “Content-Type” 请求头和内容本身自动检测内容格式。
所以在大多数情况下，不需要指定响应的格式，可以使用 `getData()` 方法或 `data` 属性来解析相应的内容。 
继续上面的例子，我们通过下面的方式获得响应数据：

```php
$responseData = $response->getData(); // get all articles
count($response->data) // count articles
$article = $response->data[0] // get first article
```


## 使用原始内容（RAW CONTENT）

当无法使用内置的格式时。 可以为HTTP请求指定原始内容，也可以处理响应的原始内容。 例如：

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'http://example.com/api/1.0']);
$response = $client->createRequest()
    ->setUrl('articles/search')
    ->addHeaders(['content-type' => 'application/json'])
    ->setContent('{query_string: "Yii"}')
    ->send();

echo 'Search results:<br>';
echo $response->content;
```

[[\yii\httpclient\Request]] 如果 `content` 没有被设置，那么 `data` 才会被自动指定格式。
[[\yii\httpclient\Response]] 如果 `data` 属性有设置，那么 `content` 才会被自动解析。


## 预配置请求和响应对象

如果使用单一的 [[\yii\httpclient\Client]] 实例处理多个类似的请求，例如，在使用 REST API 时，可以简化代码，直接配置请求和响应对象的格式即可。 通过配置 [[\yii\httpclient\Client]] 的 `requestConfig` 和 `responseConfig` 来完成。
例如：创建特定的客户端，从而配置所有的请求和响应格式为 JSON ：

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

> 提示：可以为请求和响应对象指定自定义的类，以便使用配置数组中的 'class' 键来实现所需的一些额外功能。


## 使用标头 （HTTP HEADER）

可以使用 `setHeaders()` 和 `addHeaders()` 方法指定请求头信息。 可以使用 `getHeaders()` 方法或 `headers` 属性来获取已经定义的请求头信息，并应用到 [[\yii\web\HeaderCollection]] 实例。 例如：

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'http://example.com/api/1.0']);
$request = $client->createRequest()
    ->setHeaders(['content-type' => 'application/json'])
    ->addHeaders(['user-agent' => 'My User Agent']);

$request->getHeaders()->add('accept-language', 'en-US;en');
$request->headers->set('user-agent', 'User agent override');
```

一旦创建一个响应对象，便可以通过 `getHeaders()` 方法或 `headers` 属性获取所有的响应头信息：

```php
$response = $request->send();
echo $response->getHeaders()->get('content-type');
echo $response->headers->get('content-encoding');
```


## 使用 cookies

虽然Cookies只作为请求头信息传输， [[\yii\httpclient\Request]] 和 [[\yii\httpclient\Request]] 提供了分离的接口来使用 [[\yii\web\Cookie]] 和 [[\yii\web\CookieCollection]]。

可以使用 `setCookies()` 或 `addCookies()` 方法指定请求 Cookie 。
可以使用 `getCookies()` 方法或 `cookies` 属性将已经定义的 Cookie 应用到 [[\yii\web\CookieCollection] 实例。 例如：

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

一旦创建一个响应对象，便可以使用 `getCookies()` 方法或 `cookies` 属性访问所有响应Cookie：

```php
$response = $request->send();
echo $response->getCookies()->get('country');
echo $response->headers->get('PHPSESSID');
```

可以通过简单的复制，让响应中的 Cookie 作为请求的 Cookie。
例如：假设我们需要在某些 Web 应用程序中编辑用户配置文件，该用户配置文件仅在登录后可用，因此我们需要先登录，然后使用已创建的会话进行进一步的操作：

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'http://example.com']);

$loginResponse = $client->post('login', [
    'username' => 'johndoe',
    'password' => 'somepassword',
])->send();

// $loginResponse->cookies->get('PHPSESSID') - holds the new session ID

$client->post('account/profile', ['birthDate' => '10/11/1982'])
    ->setCookies($loginResponse->cookies) // transfer response cookies to request
    ->send();
```
