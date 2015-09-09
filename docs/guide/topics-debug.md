Using the HTTP Client DebugPanel
================================

The yii2 HTTP Client  extension provides a debug panel that can be integrated with the yii debug module
and shows the executed HTTP requests.

Add the following to you application config to enable it (if you already have the debug module
enabled, it is sufficient to just add the panels configuration):

```php
    // ...
    'bootstrap' => ['debug'],
    'modules' => [
        'debug' => [
            'class' => 'yii\\debug\\Module',
            'panels' => [
                'httpclient' => [
                    'class' => 'yii\\httpclient\\debug\\HttpClientPanel',
                ],
            ],
        ],
    ],
    // ...
```

This panel allows you to execute a logged HTTP request to see its response. You may get the response as
a text representation or pass it directly to the browser.

> Note: only regular logged HTTP requests can be executed via debug panel, requests sending in batch can not.
  Also keep in mind that content of logged request can be trimmed according to [[\yii\httpclient\Client::contentLoggingMaxSize]],
  so its execution may fail or produce unexpected results.
