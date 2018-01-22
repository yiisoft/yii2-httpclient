*Транспорты*
==========

[[\yii\httpclient\Client]] предоставляет несколько различных способов отправки HTTP-сообщения в виде транспортов.
Предопределенные транспорты:

 - [[\yii\httpclient\StreamTransport]] - отправляет HTTP-сообщения, используя [потоки](http://php.net/manual/ru/book.stream.php).
   Этот транспорт используется по умолчанию. Он не требует каких-либо установленных дополнительных PHP расширений или библиотек,
   но не поддерживает расширенные функции, такие как пакетная отправка.
 - [[\yii\httpclient\CurlTransport]] - отправляет HTTP-сообщения, используя [cURL](http://php.net/manual/ru/book.curl.php)
   Для этого транспорта требуется установленное PHP расширение 'curl', но он поддерживает такие функции, как 
   пакетная отправка.

Вы можете настроить транспорт, который будет использоваться конкретным клиентом, используя [[\yii\httpclient\Client::$transport]]:

```php
use yii\httpclient\Client;

$client = new Client([
    'transport' => 'yii\httpclient\CurlTransport'
]);
```


## Создание собственного транспорта

Вы можете создать свой собственный транспорт, который будет выполнять отправку сообщений по-своему. Для этого вам следует 
расширить класс [[\yii\httpclient\Transport]] и, по крайней мере, реализовать метод `send()`. Всё, что вам нужно сделать,
это определить содержимое и заголовки HTTP-ответа, после чего можете создать для них объект ответа, используя 
[[\yii\httpclient\Client::createResponse()]]:

```php
use yii\httpclient\Transport;

class MyTransport extends Transport
{
    /**
     * @inheritdoc
     */
    public function send($request)
    {
        $responseContent = '???';
        $responseHeaders = ['???'];

        return $request->client->createResponse($responseContent, $responseHeaders);
    }
}
```

Вы так же можете переопределить метод `batchSend()`, если есть способ отправить несколько запросов с более высокой производительностью,
например, посылая их асинхронно параллельно.
