Podstawy użytkowania
====================

W celu wysłania żądania HTTP konieczne jest zainicjowanie obiektu [[\yii\httpclient\Client]] i użycie jego metody `createRequest()`. 
Następnie należy skonfigurować wszystkie parametry tego żądania według własnych potrzeb i tak przygotowane wysłać. 
W rezultacie otrzymujemy obiekt [[\yii\httpclient\Response]], który przechowuje wszystkie informacje odpowiedzi.  
Przykładowo:

```php
use yii\httpclient\Client;

$client = new Client();
$response = $client->createRequest()
    ->setMethod('POST')
    ->setUrl('http://example.com/api/1.0/users')
    ->setData(['name' => 'John Doe', 'email' => 'johndoe@example.com'])
    ->send();
if ($response->isOk) {
    $newUserId = $response->data['id'];
}
```

Aby uprościć przygotowywanie żądania, można użyć skrótowych metod `get()`, `post()`, `put()`, itd.
W przypadku korzystania z pojedynczej instancji [[\yii\httpclient\Client]] dla wielu żądań skierowanych do tej samej domeny 
(np. łącząc się z REST API), można ustawić tę domenę jako wartość właściwości `baseUrl`. Pozwala to na określanie jedynie 
relatywnych adresów URL podczas tworzenia żądania.
Dzięki temu kilka następujących po sobie żądań skierowanych do jakiegoś REST API może wyglądać jak poniżej:

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'http://example.com/api/1.0']);

$newUserResponse = $client->post('users', ['name' => 'John Doe', 'email' => 'johndoe@example.com'])->send();
$articleResponse = $client->get('articles', ['name' => 'Yii 2.0'])->send();
$client->post('subscriptions', ['user_id' => $newUserResponse->data['id'], 'article_id' => $articleResponse->data['id']])->send();
```


## Korzystanie z różnych formatów treści

Dane żądania HTTP są domyślnie przesyłane w formacie 'form-urlencoded', np. `param1=value1&param2=value2`.
Jest on powszechnie wykorzystywany w formularzach web, ale już nie dla REST API, które zwykle wymaga treści w formacie 
JSON lub XML. Format żądania można ustawić za pomocą właściwości `format` lub metody `setFormat()`.
Wspierane są następujące formaty:

 - [[\yii\httpclient\Client::FORMAT_JSON]] - format JSON,
 - [[\yii\httpclient\Client::FORMAT_URLENCODED]] - łańcuch znaków kwerendy zakodowany wg wytycznych dokumentu RFC1738,
 - [[\yii\httpclient\Client::FORMAT_RAW_URLENCODED]] - łańcuch znaków kwerendy zakodowany z opcjami predefiniowanej stałej PHP_QUERY_RFC3986,
 - [[\yii\httpclient\Client::FORMAT_XML]] - format XML.

Przykładowo:

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'http://example.com/api/1.0']);
$response = $client->createRequest()
    ->setFormat(Client::FORMAT_JSON)
    ->setUrl('articles/search')
    ->setData([
        'query_string' => 'Yii',
        'filter' => [
            'date' => ['>' => '2015-08-01']
        ],
    ])
    ->send();
```

Obiekt odpowiedzi automatycznie rozpoznaje jej format, opierając się na nagłówku 'Content-Type' i samej treści, zatem 
w większości przypadków nie jest wymagane określanie tego formatu, wystarczy przetworzyć ją używając metody `getData()`
lub właściwości `data`. Rozwijając powyższy przykład, można otrzymać dane odpowiedzi następująco:

```php
$responseData = $response->getData(); // pobierz wszystkie artykuły
count($response->data) // policz artykuły
$article = $response->data[0] // pobierz pierwszy artykuł
```


## Praca z niesformatowaną treścią

Korzystanie z wbudowanych formaterów nie jest obowiązkowe. Można skonfigurować żądanie HTTP, aby przekazywało 
niesformatowaną treść i jednocześnie można przetwarzać niesformatowaną treść uzyskaną w odpowiedzi. Dla przykładu:

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'http://example.com/api/1.0']);
$response = $client->createRequest()
    ->setUrl('articles/search')
    ->addHeaders(['content-type' => 'application/json'])
    ->setContent('{query_string: "Yii"}')
    ->send();

echo 'Search results:<br>';
echo $response->content;
```

[[\yii\httpclient\Request]] formatuje przekazywane `data`, tylko jeśli `content` nie jest ustawiony.
[[\yii\httpclient\Response]] przetwarza `content`, tylko jeśli `data` jest wywołane.


## Prekonfiguracja obiektów żądania i odpowiedzi

Używając pojedynczej instancji [[\yii\httpclient\Client]] dla kilku podobnych żądań, przykładowo pracując z REST API, 
można uprościć i przyspieszyć pisanie kodu, dodając własną konfigurację dla obiektów żądania i odpowiedzi. Można to 
zrobić korzystając z pól `requestConfig` i `responseConfig` w [[\yii\httpclient\Client]].
Przykładowo, aby ustawić format JSON dla wszystkich żądań klienta:

```php
use yii\httpclient\Client;

$client = new Client([
    'baseUrl' => 'http://example.com/api/1.0',
    'requestConfig' => [
        'format' => Client::FORMAT_JSON
    ],
    'responseConfig' => [
        'format' => Client::FORMAT_JSON
    ],
]);

$request = $client->createRequest();
echo $request->format; // zwraca: 'json'
```

> Tip: można nawet określić własne klasy dla obiektów żądania i odpowiedzi, aby uzyskać dodatkowe funkcjonalności - 
  w tym celu należy użyć klucza 'class' w tablicy konfiguracyjnej.


## Praca z nagłówkami

Można zdefiniować nagłówki żądania za pomocą metod `setHeaders()` i `addHeaders()`.
Metoda `getHeaders()` lub właściwość `headers` służy do pobrania już zdefiniowanych nagłówków w postaci instancji klasy 
[[\yii\web\HeaderCollection]]. Przykładowo:

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'http://example.com/api/1.0']);
$request = $client->createRequest()
    ->setHeaders(['content-type' => 'application/json'])
    ->addHeaders(['user-agent' => 'My User Agent']);

$request->getHeaders()->add('accept-language', 'en-US;en');
$request->headers->set('user-agent', 'User agent override');
```

Kiedy obiekt odpowiedzi jest już gotowy, można pobrać wszystkie nagłówki odpowiedzi za pomocą metody `getHeaders()` lub 
właściwości `headers`:

```php
$response = $request->send();
echo $response->getHeaders()->get('content-type');
echo $response->headers->get('content-encoding');
```


## Praca z ciasteczkami

Pomimo tego, że ciasteczka są przesyłane wyłącznie w nagłówkach, [[\yii\httpclient\Request]] i [[\yii\httpclient\Request]]
dostarczają oddzielny interfejs dla pracy z nimi, używając [[\yii\web\Cookie]] i [[\yii\web\CookieCollection]].

Można zdefiniować ciasteczka żądania używając metod `setCookies()` lub `addCookies()`.
Metoda `getCookies()` lub właściwość `cookies` służy do pobrania już zdefiniowanych ciasteczek w postaci instancji klasy 
[[\yii\web\CookieCollection]]. Przykładowo:

```php
use yii\httpclient\Client;
use yii\web\Cookie;

$client = new Client(['baseUrl' => 'http://example.com/api/1.0']);
$request = $client->createRequest()
    ->setCookies([
        ['name' => 'country', 'value' => 'USA'],
        new Cookie(['name' => 'language', 'value' => 'en-US']),
    ])
    ->addCookies([
        ['name' => 'view_mode', 'value' => 'full']
    ]);

$request->cookies->add(['name' => 'display-notification', 'value' => '0']);
```

Kiedy obiekt odpowiedzi jest już gotowy, można pobrać wszystkie ciasteczka odpowiedzi za pomocą metody `getCookies()` lub 
właściwości `cookies`:

```php
$response = $request->send();
echo $response->getCookies()->get('country');
echo $response->headers->get('PHPSESSID');
```

Można przekazać ciasteczka pomiędzy obiektem odpowiedzi i żądania po prostu je kopiując.
Przykładowo, musimy edytować profil użytkownika w aplikacji web, dostępnej jedynie po zalogowaniu - konieczne jest zatem 
zalogowanie się i następnie użycie nowoutworzonej sesji dla uwierzytelnienia dalszej aktywności:

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'http://example.com']);

$loginResponse = $client->post('login', [
    'username' => 'johndoe',
    'password' => 'somepassword',
])->send();

// $loginResponse->cookies->get('PHPSESSID') - przechowuje ID nowej sesji

$client->post('account/profile', ['birthDate' => '10/11/1982'])
    ->setCookies($loginResponse->cookies) // przekazuje ciasteczka odpowiedzi do żądania
    ->send();
```
