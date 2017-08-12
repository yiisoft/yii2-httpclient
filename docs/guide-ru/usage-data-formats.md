Форматы данных
============

Формат данных определяет способ составления и разбора HTTP-сообщения, например как следует конвертировать 
[[\yii\httpclient\Message::data]] в [[\yii\httpclient\Message::content]] и обратно.

По умолчанию поддерживаются следующие форматы:

 - [[\yii\httpclient\Client::FORMAT_JSON]] - формат JSON
 - [[\yii\httpclient\Client::FORMAT_URLENCODED]] - *urlencoded строка запроса по RFC1738*
 - [[\yii\httpclient\Client::FORMAT_RAW_URLENCODED]] - *urlencoded строка запроса по PHP_QUERY_RFC3986*
 - [[\yii\httpclient\Client::FORMAT_XML]] - формат XML

Каждый формат представлен в виде двух объектов: *'formatter'* и *'parser'*. *Formatter* определяет, как содержимое запроса должно быть составлено из данных. 
*Parser* определяет, как содержимое сырого ответа должно преобразовываться в данные.

[[\yii\httpclient\Client]] автоматически выбирает соответствующий *formatter* и *parser* для указанных выше форматов.
Однако, Вы можете изменить это поведение,  используя [[\yii\httpclient\Client::formatters]] и [[\yii\httpclient\Client::parsers]].
При помощи этих свойств, Вы можете добавлять свои собственные форматы и или изменять стандартные.
Например:

```php
use yii\httpclient\Client;

$client = new Client([
    'formatters' => [
        'myformat' => 'app\components\http\MyFormatter', // добавить новый formatter
        Client::FORMAT_XML => 'app\components\http\MyXMLFormatter', // переопределить дефолтный XML formatter
    ],
]);
```

При создании  собственного *parser'а* вам необходимо реализовать [[\yii\httpclient\ParserInterface]], 
при создании *formatter'а* - [[\yii\httpclient\FormatterInterface]]. Например:

```php
use yii\httpclient\FormatterInterface;
use yii\httpclient\ParserInterface;
use yii\httpclient\Response;

class ParserIni implements ParserInterface
{
    public function parse(Response $response)
    {
        return parse_ini_string($response->content);
    }
}

class FormatterIni implements FormatterInterface
{
    public function format(Request $request)
    {
        $request->getHeaders()->set('Content-Type', 'text/ini; charset=UTF-8');

        $pairs = [];
        foreach ($request->data as $name => $value) {
            $pairs[] = "$name=$value";
        }

        $request->setContent(implode("\n", $pairs));
        return $request;
    }
}
```
