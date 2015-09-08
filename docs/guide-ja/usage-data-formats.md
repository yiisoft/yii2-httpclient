データ形式
==========

データ形式が HTTP メッセージのコンテントを作成または解析する方法を決定します。
言い換えると、データ形式によって、[[\yii\httpclient\Message::data]] と [[\yii\httpclient\Message::content]] が相互にどのように変換されるべきかが決定されます。

次の形式がデフォルトでサポートされています。

 - [[\yii\httpclient\Client::FORMAT_JSON]] - JSON 形式
 - [[\yii\httpclient\Client::FORMAT_URLENCODED]] - RFC1738 によって urlencode されたクエリ文字列
 - [[\yii\httpclient\Client::FORMAT_RAW_URLENCODED]] - PHP_QUERY_RFC3986 によって urlencode されたクエリ文字列
 - [[\yii\httpclient\Client::FORMAT_XML]] - XML 形式

それぞれの形式は二つの実体、'formatter' と 'parser' によってカバーされます。
Formatter は、リクエストのコンテントがデータから作成される方法を決定します。
Parser は、生のレスポンスコンテントがデータに解析される方法を決定します。

[[\yii\httpclient\Client]] は、上述の形式すべてについて、自動的に対応する formatter と parser を選択します。
ただし、この振る舞いは、[[\yii\httpclient\Client::formatters]] と [[\yii\httpclient\Client::parsers]] を使って変更することが出来ます。
これらのフィールドによって、あなた自身の形式を追加したり、標準的な形式を変更したりすることが出来ます。
例えば、

```php
use yii\httpclient\Client;

$client = new Client([
    'formatters' => [
        'myformat' => 'app\components\http\MyFormatter', // 新しい formatter を追加
        Client::FORMAT_XML => 'app\components\http\MyXMLFormatter', // デフォルトの XML formatter をオーバーライド
    ],
]);
```

あなた自身の parser を作成するときは [[\yii\httpclient\ParserInterface]] を実装しなければなりません。
formatter の場合は  [[\yii\httpclient\ParserInterface]] です。
例えば、

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
