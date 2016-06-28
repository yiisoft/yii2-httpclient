Basic Usage
===========

In order to send HTTP requests, you'll need to instantiate [[\yii\httpclient\Client]] and use its
`createRequest()` method to create new HTTP request. Then you should configure all request parameters
according to your goal and send request. As the result you'll get a [[\yii\httpclient\Response]] instance,
which holds all response information and data.
For example:

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

You may use shortcut methods `get()`, `post()`, `put()` and so on to simplify new request preparation.
If you are using single [[\yii\httpclient\Client]] instance for multiple request to the same domain (for
example in case of using REST API), you may setup its `baseUrl` property with this domain. This will
allow you to specify only the relative URL while creating a new request.
Thus the several request to some REST API may look like following:

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'http://example.com/api/1.0']);

$newUserResponse = $client->post('users', ['name' => 'John Doe', 'email' => 'johndoe@example.com'])->send();
$articleResponse = $client->get('articles', ['name' => 'Yii 2.0'])->send();
$client->post('subscriptions', ['user_id' => $newUserResponse->data['id'], 'article_id' => $articleResponse->data['id']])->send();
```


## Using different content formats

By default HTTP request data is send as 'form-urlencoded', e.g. `param1=value1&param2=value2`.
This is a common format for the web forms, but not for the REST API, which usually demands content
should be in JSON or XML format. You may setup format being used for request content using `format`
property or `setFormat()` method.
Following formats are supported:

 - [[\yii\httpclient\Client::FORMAT_JSON]] - JSON format
 - [[\yii\httpclient\Client::FORMAT_URLENCODED]] - urlencoded by RFC1738 query string
 - [[\yii\httpclient\Client::FORMAT_RAW_URLENCODED]] - urlencoded by PHP_QUERY_RFC3986 query string
 - [[\yii\httpclient\Client::FORMAT_XML]] - XML format

For example:

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

The response object detects content format automatically based on 'Content-Type' header and content itself.
So in most cases you don't need to specify format for response, you can parse it simply using `getData()`
method or `data` property. Continuing the above example, we can get response data in the following way:

```php
$responseData = $response->getData(); // get all articles
count($response->data) // count articles
$article = $response->data[0] // get first article
```


## Working with raw content

No one is forcing you to rely on the built-in formats. You can specify raw content for your HTTP request
as well as you can process a raw content of the response. For example:

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

[[\yii\httpclient\Request]] formats specified `data` only if `content` is not set.
[[\yii\httpclient\Response]] parses the `content` only if `data` is requested.


## Pre-configure request and response objects

If you are using single instance of [[\yii\httpclient\Client]] for several similar requests,
for example, while working with REST API, you may simplify and speed up your code declaring
your own configuration for request and response objects. This can be done via `requestConfig`
and `responseConfig` fields of [[\yii\httpclient\Client]].
For example: you may want to setup JSON format for all request, created by particular client:

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

> Tip: you may even specify your own classes for the request and response objects to facilitate
  some extra functionality you need, using 'class' key in configuration array.


## Working with headers

You may specify request headers using `setHeaders()` and `addHeaders()` methods.
You may use `getHeaders()` method or `headers` property to get already defined headers as
[[\yii\web\HeaderCollection]] instance. For example:

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'http://example.com/api/1.0']);
$request = $client->createRequest()
    ->setHeaders(['content-type' => 'application/json'])
    ->addHeaders(['user-agent' => 'My User Agent']);

$request->getHeaders()->add('accept-language', 'en-US;en');
$request->headers->set('user-agent', 'User agent override');
```

Once you have a response object you can access all response headers using `getHeaders()` method
or `headers` property:

```php
$response = $request->send();
echo $response->getHeaders()->get('content-type');
echo $response->headers->get('content-encoding');
```


## Working with cookies

Although Cookies are transferred just as header values, [[\yii\httpclient\Request]] and [[\yii\httpclient\Request]]
provides separated interface to work with them using [[\yii\web\Cookie]] and [[\yii\web\CookieCollection]].

You may specify request Cookies using `setCookies()` or `addCookies()` methods.
You may use `getCookies()` method or `cookies` property to get already defined Cookies as
[[\yii\web\CookieCollection]] instance. For example:

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

Once you have a response object you can access all response Cookies using `getCookies()` method
or `cookies` property:

```php
$response = $request->send();
echo $response->getCookies()->get('country');
echo $response->headers->get('PHPSESSID');
```

You may transfer Cookies from response object to request using simple copy.
For example: assume we need to edit user profile at some web application, which is available only
after login, so we need to log in first and use created session for further activity:

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
