Logowanie i profilowanie
========================

To rozszerzenie pozwala na zalogowanie wysyłanych żądań HTTP i profilowanie ich wykonywania.
Aby skonfigurować cel logu, który będzie przechwytywał wszystkie wydarzenia związane z żądaniami HTTP, należy użyć 
kategorii `yii\httpclient\Transport*`. Przykładowo:

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

Można również użyć [panelu debugowania klienta HTTP](topics-debug.md), aby przejrzeć wszystkie powiązane logi.

> Attention: treść niektórych żądań HTTP może być wyjątkowo długa i z tego powodu zapis ich w całości w logach może być 
  problematyczny. Maksymalna długość treści żądania, która może być umieszczona w logu jest kontrolowana za pomocą 
  wartości [[\yii\httpclient\Client::contentLoggingMaxSize]]. Nadmiarowa treść jest ucinana przed umieszczeniem w logu.
