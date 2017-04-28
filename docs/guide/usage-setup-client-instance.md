Setup Client Instance
=====================

Using of this extension starts from instantiating [[\yii\httpclient\Client]] object. There are several ways
you can integrate [[\yii\httpclient\Client]] to your program. Here the most common approaches are described.


## Setup client as application component

[[\yii\httpclient\Client]] extends [[\yii\base\Component]] and thus it can be setup at [[\yii\di\Container]]
level: as module or application component. For example:

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


## Extending client class

Since [[\yii\httpclient\Client]] can be used as application component, you can just extend it, adding some
custom logic, which you need. For example:

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


## Wrapping client object

You may use [[\yii\httpclient\Client]] instance as internal field for component, which provides some complex
functionality. For example:

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
