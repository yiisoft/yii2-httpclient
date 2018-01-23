イベント
========

[[\yii\httpclient\Request]] は、いくつかのイベントを提供します。
それらは、イベントハンドラまたはビヘイビアによって処理することが出来ます。

- [[\yii\httpclient\Request::EVENT_BEFORE_SEND]] - リクエスト送信の前に発生。
- [[\yii\httpclient\Request::EVENT_AFTER_SEND]] - リクエスト送信の後に発生。

これらのイベントを利用して、リクエストのパラメータまたは受信したレスポンスを修正することが出来ます。
例えば、

```php
use yii\httpclient\Client;
use yii\httpclient\Request;
use yii\httpclient\RequestEvent;

$client = new Client();

$request = $client->createRequest()
    ->setMethod('get')
    ->setUrl('http://api.domain.com')
    ->setData(['param' => 'value']);

// 最終的なデータセットに基づくシグニチャの生成を保証する
$request->on(Request::EVENT_BEFORE_SEND, function (RequestEvent $event) {
    $data = $event->request->getData();

    $signature = md5(http_build_query($data));
    $data['signature'] = $signature;

    $event->request->setData($data);
});

// レスポンスデータを正規化する
$request->on(Request::EVENT_AFTER_SEND, function (RequestEvent $event) {
    $data = $event->response->getData();

    $data['content'] = base64_decode($data['encoded_content']);

    $event->response->setData($data);
});

$response = $request->send();
```

[[\yii\httpclient\Request]] のインスタンスにイベントハンドラをアタッチするのは、あまり現実的ではありません。
同じユースケースは、[[\yii\httpclient\Client]] クラスのイベントを使って処理することが出来ます。

- [[\yii\httpclient\Client::EVENT_BEFORE_SEND]] - リクエスト送信の前に発生。
- [[\yii\httpclient\Client::EVENT_AFTER_SEND]] - リクエスト送信の後に発生。

これらのイベントは、クライアントによって生成されるすべてのリクエストに対して、[[\yii\httpclient\Request]] が発生させるイベントと同じタイミングとシグニチャを持って発生させられます。
例えば、

```php
use yii\httpclient\Client;
use yii\httpclient\RequestEvent;

$client = new Client();

$client->on(Client::EVENT_BEFORE_SEND, function (RequestEvent $event) {
    // ...
});
$client->on(Client::EVENT_AFTER_SEND, function (RequestEvent $event) {
    // ...
});
```

> Note: [[\yii\httpclient\Client]] と [[\yii\httpclient\Request]] は、`EVENT_BEFORE_SEND` および
  `EVENT_AFTER_SEND` のイベントについて、同じ名前を共有します。
  従って、両方のクラスに適用できるビヘイビアを作成することが可能です。
