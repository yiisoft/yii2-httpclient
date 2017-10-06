Параметры запроса
===============

Вы можете использовать [[\yii\httpclient\Request::options]] для настройки выполнения конкретного запроса.
Поддерживаются следующие параметры:
 - timeout: integer, максимально разрешенное количество секунд на выполнение запроса.
 - proxy: string, URI, определяющий адрес прокси-сервера. (например tcp://proxy.example.com:5100).
 - userAgent: string, содержимое заголовка "User-Agent: ", которое будет использоваться в HTTP-запросе.
 - followLocation: boolean, следует ли следовать любому заголовку "Location: ", который сервер отправляет, как часть HTTP-заголовка.
 - maxRedirects: integer, *максимальное количество следуемых редиректов*.
 - sslVerifyPeer: boolean, следует ли выполнить проверку сертификата *удалённого узла*.
 - sslCafile: string, расположение файла центра сертификации в локальной файловой системе, должно использоваться совместно с 
   параметром 'sslVerifyPeer', что бы провести проверку подлинности *удалённого узла*.
 - sslCapath: string, директория, которая содержит несколько CA сертификатов.

Например:

```php
use yii\httpclient\Client;

$client = new Client();

$response = $client->createRequest()
    ->setMethod('post')
    ->setUrl('http://domain.com/api/1.0/users')
    ->setData(['name' => 'John Doe', 'email' => 'johndoe@domain.com'])
    ->setOptions([
        'proxy' => 'tcp://proxy.example.com:5100', // используем прокси
        'timeout' => 5, // устанавливаем тайм-аут в 5 секунд, если сервер не отвечает
    ])
    ->send();
```

> Tip: Вы можете настроить параметры запроса по умолчанию через свойство [[\yii\httpclient\Client::requestConfig]]. 
  Если, настроив параметры запроса по умолчанию, вам понадобится добавить дополнительные специфические параметры для 
  конкретного  запроса, то используйте метод [[\yii\httpclient\Request::addOptions()]], сохраняя при этом значения 
  параметров запроса, указанных по умолчанию ранее.

Вы также можете передавать параметры, которые специфичны для конкретного транспорта запросов. Обычно это происходит в 
случае использования [[\yii\httpclient\CurlTransport]]. Например: вы хотите указать тайм-аут отдельно для 
соединения и получения данных, который поддерживается PHP-библиотекой cURL. Вы можете сделать это следующим образом:

```php
use yii\httpclient\Client;

$client = new Client([
    'transport' => 'yii\httpclient\CurlTransport' //только cURL поддерживает нужные нам параметры
]);

$response = $client->createRequest()
    ->setMethod('post')
    ->setUrl('http://domain.com/api/1.0/users')
    ->setData(['name' => 'John Doe', 'email' => 'johndoe@domain.com'])
    ->setOptions([
        CURLOPT_CONNECTTIMEOUT => 5, // тайм-аут подключения
        CURLOPT_TIMEOUT => 10, // тайм-аут получения данных
    ])
    ->send();
```

Подробные сведения о поддержке конкретных опций смотрите в документации конкретного класса транспорта.