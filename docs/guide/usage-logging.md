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

> Attention: since content of the some HTTP requests may be very long, saving it in full inside the logs
  may lead to certain problems. Thus there is a restriction on the maximum length of the request content,
  which will be placed in log. It is controlled by [[\yii\httpclient\Client::contentLoggingMaxSize]].
  Any exceeding content will be trimmed before logging.
