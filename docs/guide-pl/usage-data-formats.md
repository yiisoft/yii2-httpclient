Formatowanie danych
===================

Format danych decyduje o sposobie w jaki treść wiadomości HTTP powinna być skomponowana lub przetworzona, w szczególności 
w jaki sposób [[\yii\httpclient\Message::$data]] powinna być przekonwertowana na [[\yii\httpclient\Message::$content]] i vice versa.

Domyślnie wspierane są poniższe typy formatowania:

 - [[\yii\httpclient\Client::FORMAT_JSON]] - format JSON,
 - [[\yii\httpclient\Client::FORMAT_URLENCODED]] - łańcuch znaków kwerendy zakodowany wg wytycznych dokumentu RFC1738,
 - [[\yii\httpclient\Client::FORMAT_RAW_URLENCODED]] -łańcuch znaków kwerendy zakodowany z opcjami predefiniowanej stałej PHP_QUERY_RFC3986,
 - [[\yii\httpclient\Client::FORMAT_XML]] - format XML.

Każdy format składa się z dwóch jednostek: 'formatera' i 'parsera'. Formater ustala w jaki sposób treść żądania 
powinna zostać skomponowana z podanych danych. Parser ustala w jaki sposób surowa treść odpowiedzi powinna być przetworzona na dane.

[[\yii\httpclient\Client]] automatycznie wybiera odpowiedni formater i parser dla wszystkich powyższcyh tyów formatowania, 
ale można wpłynąć na ten mechanizm za pomocą [[\yii\httpclient\Client::$formatters]] i [[\yii\httpclient\Client::$parsers]].
Dzięki tym polom można dodać własne typy formatowania lub zmienić standardowe.
Dla przykładu:

```php
use yii\httpclient\Client;

$client = new Client([
    'formatters' => [
        'myformat' => 'app\components\http\MyFormatter', // dodaj nowy formater
        Client::FORMAT_XML => 'app\components\http\MyXMLFormatter', // przeciąż domyślny formater XML
    ],
]);
```

Tworząc własny parser należy zaimplementować [[\yii\httpclient\ParserInterface]], a tworząc formater - 
[[\yii\httpclient\FormatterInterface]]. Przykładowo:

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
