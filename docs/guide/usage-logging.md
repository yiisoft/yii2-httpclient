Logging and Profiling
=====================

This extension allows logging HTTP requests being sent and profiling their execution.
In order to setup a log target, which can capture all entries related to HTTP requests, you should
use category `yii\httpclient\Transport*`. For example:

```php
return [
    // ...
    'components' => [
        // ...
        'log' => [
            // ...
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/http-request.log',
                    'categories' => ['yii\httpclient\*'],
                ],
                // ...
            ],
        ],
    ],
];
```

You may also use [HTTP client DebugPanel](topics-debug.md) to see all related logs.

> Attention: since the content of some HTTP requests may be very long, saving it in full inside the logs
  may lead to certain problems. Thus there is the restriction on the maximum length of the request content,
  which will be placed in log. It is controlled by [[\yii\httpclient\Client::$contentLoggingMaxSize]].
  Any exceeding content will be trimmed before logging.

Since version 2.0.18, `Authorization`, `Proxy-Authorization`, and `Cookie` header values are replaced
with `***` in log and profile messages. Header names are matched case-insensitively. The original
headers are still sent to the server. To mask additional headers, extend
[[\yii\httpclient\Client::$sensitiveHeaders]]:

```php
$client = new \yii\httpclient\Client();
$client->sensitiveHeaders[] = 'X-Api-Key';
```

Setting `sensitiveHeaders` to an empty array restores unmasked header logging. URL parameters and
request bodies are not masked by this setting; override `createRequestLogToken()` if they contain
sensitive values that must be removed from logs.
