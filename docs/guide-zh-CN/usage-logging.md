日志及分析
=====================

通过配置日志记录 HTTP 发送的请求并分析其执行情况。
在配置日志时，配置日志类别应类似 `yii\httpclient\Transport*` 。 例如：

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

还可以通过 [使用调试面板](topics-debug.md) 查看所有相关日志。

> 注意：由于一些 HTTP 请求的内容可能非常长，将其完全保存在日志中可能会导致某些问题。 因此，对请求内容的最大长度存在限制。 通过 [[\yii\httpclient\Client::$contentLoggingMaxSize]] 控制。 任何超出长度的内容都会被丢弃。