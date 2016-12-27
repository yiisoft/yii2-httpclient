配置客户端实例
=====================

有几种不同的方法可以将 [[\yii\httpclient\Client]] 集成到程序中。 这里演示最常见的方法。


## 通过应用组建配置客户端

[[\yii\httpclient\Client]] 继承了 [[\yii\base\Component]] ，因此可以在 [[\yii\di\Container]] 的级别进行设置：作为模块集成或应用程序组件集成。 例如：

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


## 扩展客户端类

只要 [[\yii\httpclient\Client]] 被作为组件集成至应用，便可对它进行扩展，比如添加所需的自定义逻辑，例如：

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


## 封装客户端为组件

可以将 [[\yii\httpclient\Client]] 实例封装为组件，可以实现更复杂的功能。 例如：

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
