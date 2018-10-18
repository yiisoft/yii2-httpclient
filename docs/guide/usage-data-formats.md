Data Formats
============

Data format determines the way content of HTTP message should be composed or parsed, e.g. it determines
how [[\yii\httpclient\Message::$data]] should be converted into [[\yii\httpclient\Message::$content]] and vice versa.

Following formats are supported by default:

 - [[\yii\httpclient\Client::FORMAT_JSON]] - JSON format
 - [[\yii\httpclient\Client::FORMAT_URLENCODED]] - urlencoded by RFC1738 query string
 - [[\yii\httpclient\Client::FORMAT_RAW_URLENCODED]] - urlencoded by PHP_QUERY_RFC3986 query string
 - [[\yii\httpclient\Client::FORMAT_XML]] - XML format

Each format is covered by two entities: 'formatter' and 'parser'. Formatter determines the way content of the
request should be composed from data. Parser determines how raw response content should be parsed into data.

[[\yii\httpclient\Client]] automatically chooses corresponding formatter and parser for all format mentioned above.
However you can alter this behavior using [[\yii\httpclient\Client::$formatters]] and [[\yii\httpclient\Client::$parsers]].
With these fields you can add you own formats or alter standard ones.
For example:

```php
use yii\httpclient\Client;

$client = new Client([
    'formatters' => [
        'myformat' => 'app\components\http\MyFormatter', // add new formatter
        Client::FORMAT_XML => 'app\components\http\MyXMLFormatter', // override default XML formatter
    ],
    'parsers' => [
        // configure options of the JsonParser, parse JSON as objects
        Client::FORMAT_JSON => [
            'class' => 'yii\httpclient\JsonParser',
            'asArray' => false,
        ]
    ],
]);
```

While creating your own parser you should implement [[\yii\httpclient\ParserInterface]], while creating
formatter - [[\yii\httpclient\FormatterInterface]]. For example:

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
