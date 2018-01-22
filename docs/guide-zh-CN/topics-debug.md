使用调试面板
================================

yii2 HTTP 客户端扩展提供了一个可以与 yii 调试模块集成的调试面板，并显示已执行的HTTP 请求。

将以下内容添加到应用程序配置中以启用它（如果已启用调试模块，则只需添加面板配置即可）：

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

此面板可执行已记录的HTTP请求，并查看响应。 并可将响应作为文本直接传递到浏览器。

> 注意：只有正常记录的 HTTP 请求可以通过调试面板执行，批量请求则不可以。 并且，记录的请求的内容的最大长度由 [[\yii\httpclient\Client::$contentLoggingMaxSize]] 进行控制，因此其执行可能会失败或产生意外的结果。
