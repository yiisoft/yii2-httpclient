Konfigurowanie instancji klienta
================================

Każde użycie tego rozszerzenia należy rozpocząć od stworzenia obiektu klasy [[\yii\httpclient\Client]]. Integrację 
klienta z własną aplikacją można wykonać na kilka sposobów - poniżej prezentujemy najbardziej popularne.


## Skonfigurowanie klienta jako komponent aplikacji

[[\yii\httpclient\Client]] rozszerza [[\yii\base\Component]], dzięki czemu może być skonfigurowany za pomocą 
[[\yii\di\Container]]: jako moduł lub komponent aplikacji. Przykładowo:

```php
return [
    // ...
    'components' => [
        // ...
        'phpNetHttp' => [
            'class' => 'yii\httpclient\Client',
            'baseUrl' => 'http://uk.php.net',
        ],
    ],
];

// ...
echo Yii::$app->phpNetHttp->get('docs.php')->send()->content;
```


## Rozszerzenie klasy klienta

Ponieważ [[\yii\httpclient\Client]] można użyć jako komponentu aplikacji, można go również rozszerzyć, dodając własną 
wymaganą logikę. Dla przykładu:

```php
use yii\httpclient\Client;

class MyRestApi extends Client
{
    public $baseUrl = 'http://my.rest.api/';

    public function addUser(array $data)
    {
        $response = $this->post('users', $data)->send();
        if (!$response->isOk) {
            throw new \Exception('Nie można dodać użytkownika.');
        }
        return $response->data['id'];
    }

    // ...
}
```


## Opakowanie obiektu klienta

Można użyć instancji [[\yii\httpclient\Client]] jako wewnętrznego pola dla komponentu, który dostarczy jakiejś 
skomplikowanej funkcjonalności. Przykład:

```php
use yii\base\Component;
use yii\httpclient\Client;

class MyRestApi extends Component
{
    public $baseUrl = 'http://my.rest.api/';

    private $_httpClient;

    public function getHttpClient()
    {
        if (!is_object($this->_httpClient)) {
            $this->_httpClient = Yii::createObject([
                'class' => Client::className(),
                'baseUrl' => $this->baseUrl,
            ]);
        }
        return $this->_httpClient;
    }

    public function addUser(array $data)
    {
        $response = $this->getHttpClient()->post('users', $data)->send();
        if (!$response->isOk) {
            throw new \Exception('Nie można dodać użytkownika.');
        }
        return $response->data['id'];
    }

    // ...
}
```
