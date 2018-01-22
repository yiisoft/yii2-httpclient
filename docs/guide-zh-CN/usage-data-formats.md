数据格式
============

数据格式取决于 HTTP 消息内容组合或解析的方式，例如： 它将确定 [[\yii\httpclient\Message::$data]] 通过何种方式转换为 [[\yii\httpclient\Message::$content]]，反之亦然。

默认情况下支持以下格式：

 - [[\yii\httpclient\Client::FORMAT_JSON]] - JSON format
 - [[\yii\httpclient\Client::FORMAT_URLENCODED]] - urlencoded by RFC1738 query string
 - [[\yii\httpclient\Client::FORMAT_RAW_URLENCODED]] - urlencoded by PHP_QUERY_RFC3986 query string
 - [[\yii\httpclient\Client::FORMAT_XML]] - XML format

每个格式由2个实体覆盖： “formatter” 和 “parser” 。 Formatter 决定请求中数据的组成方式。 Parser 决定如何将原始响应内容解析为数据。

[[\yii\httpclient\Client]] 自动为上述所有格式选择相应的 formatter 和 parser 。
但是，可以使用 [[\yii\httpclient\Client::$formatters]] 和 [[\yii\httpclient\Client::$parsers]] 更改此行为。
可以使用这些字段添加自定义格式或更改标准的格式。
例如：

```php
use yii\httpclient\Client;

$client = new Client([
    'formatters' => [
        'myformat' => 'app\components\http\MyFormatter', // add new formatter
        Client::FORMAT_XML => 'app\components\http\MyXMLFormatter', // override default XML formatter
    ],
]);
```

在创建自定义的解析器时，应该实现 [[\yii\httpclient\ParserInterface]] ，同时创建formatter - [[\yii\httpclient\FormatterInterface]]。 例如：

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
        $request->getHeaders()->set('Content-Type', 'text/ini   ; charset=UTF-8');

        $pairs = []
        foreach ($request->data as $name => $value) {
            $pairs[] = "$name=$value";
        }

        $request->setContent(implode("\n", $pairs));
        return $request;
    }
}
```
