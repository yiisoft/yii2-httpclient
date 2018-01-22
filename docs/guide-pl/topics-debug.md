Korzystanie z panelu debugowania klienta HTTP
=============================================

Klient HTTP Yii 2 dostarcza panel debugowania, który można zintegrować z modułem debugowania Yii. Panel pokazuje wykonane 
żądania HTTP.

Dodaj następujący kod do konfiguracji swojej aplikacji, aby włączyć panel (jeśli masz już włączony moduł debugowania, 
wystarczy dodać konfigurację 'panels'):

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

Panel pozwala na wysłanie zalogowanego żądania HTTP i sprawdzenie jego odpowiedzi. Można otrzymać odpowiedź w postaci  
tekstu lub przekazać ją bezpośrednio do przeglądarki.

> Note: tylko standardowe zalogowane żądania HTTP mogą być wysłane przez panel debugowania - nie jest to możliwe dla 
  serii żądań. Należy także pamiętać, że treść zalogowanych żądań może być obcięta zgodnie z wartością 
  [[\yii\httpclient\Client::$contentLoggingMaxSize]], zatem wysłanie takich żądań może zakończyć się błędem lub skutkować
  niespodziewanymi rezultatami.
