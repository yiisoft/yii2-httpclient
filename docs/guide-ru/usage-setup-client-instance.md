Пример настройки клиента
=====================

Использование этого расширения начинается с создания объекта [[\yii\httpclient\Client]]. Существует несколько способов интеграции 
[[\yii\httpclient\Client]] в вашу программу. Здесь описаны наиболее распространенные подходы.


## Установка клиента, в качестве компонента приложения

[[\yii\httpclient\Client]] расширяет [[\yii\base\Component]] и, таким образом, его можно настроить на уровне [[\yii\di\Container]]: 
в качестве модуля или компонента приложения. Например:

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


## Расширение класса клиента

Так как [[\yii\httpclient\Client]] может использоваться в качестве компонента приложения, то вы можете просто расширить его, добавив свою логику. Например:

```php
use yii\httpclient\Client;

class MyRestApi extends Client
{
    public $baseUrl = 'http://my.rest.api/';

    public function addUser(array $data)
    {
        $response = $this->post('users', $data)->send();
        if (!$response->isOk) {
            throw new \Exception('Unable to add user.');
        }
        return $response->data['id'];
    }

    // ...
}
```


## Обертка для объекта клиента

Вы можете использовать экземпляр [[\yii\httpclient\Client]] в качестве внутреннего поля для компонента, который обеспечивает более сложную 
функциональность. Например:

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
            throw new \Exception('Unable to add user.');
        }
        return $response->data['id'];
    }

    // ...
}
```
