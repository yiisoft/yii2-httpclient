クライアントのインスタンスをセットアップする
============================================

このエクステンションの使用は、[[\yii\httpclient\Client]] オブジェクトをインスタンス化するところから始まります。
[[\yii\httpclient\Client]] をあなたのプログラムに統合する方法はいくつかあります。ここでは、もっとも一般的なアプローチを説明します。


## クライアントをアプリケーション・コンポーネントとしてセットアップする

[[\yii\httpclient\Client]] は [[\yii\base\Component]] の拡張ですので、[[\yii\di\Container]] のレベルで、すなわち、アプリケーション・コンポーネントとして、セットアップすることが出来ます。
例えば、

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


## クライアント・クラスを拡張する

[[\yii\httpclient\Client]] はアプリケーション・コンポーネントとして使用する事が出来るため、単にこれを拡張して、
あなたが必要とする何らかのカスタム・ロジックを追加することが出来ます。

```php
use yii\httpclient\Client;

class MyRestApi extends Client
{
    public $baseUrl = 'http://my.rest.api/';

    public function addUser(array $data)
    {
        $response = $this->post('users', $data)->send();
        if (!$response->isOk) {
            throw new \Exception('ユーザを追加することが出来ません。');
        }
        return $response->data['id'];
    }

    // ...
}
```


## クライアント・オブジェクトをラップする

[[\yii\httpclient\Client]] のインスタンスをコンポーネントの内部フィールドとして使用して、ある種の複雑な機能を提供させる事も出来ます。
例えば、

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
            throw new \Exception('ユーザを追加することが出来ません。');
        }
        return $response->data['id'];
    }

    // ...
}
```
