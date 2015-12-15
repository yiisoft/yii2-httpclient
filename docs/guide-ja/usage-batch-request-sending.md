バッチリクエスト送信
====================

HTTP クライアントは、[[\yii\httpclient\Client::batchSend()]] メソッドを使って、複数のリクエストを一度に送信することを可能にしています。

```php
use yii\httpclient\Client;

$client = new Client();

$requests = [
    $client->get('http://domain.com/keep-alive'),
    $client->post('http://domain.com/notify', ['userId' => 12]),
];
$responses = $client->batchSend($requests);
```

[トランスポート](usage-transports.md) によっては、このメソッドを使うことにより、パフォーマンスを向上させるという利点を得ることが出来ます。
内蔵のトランスポートの中では、[[\yii\httpclient\CurlTransport]] のみがこれに当てはまります。
これはリクエストを並列化して送信することが出来、それによってプログラムの実行時間を短くすることが出来ます。

> Note: `batchSend()` において、リクエストを特殊な方法で処理して何らかの利点を得ることが出来るのは、いくつかの特定のトランスポートに限られています。
  デフォルトでは、トランスポートは、エラーも警告も発することはありませんが、リクエストを一つずつ順番に送信するだけです。
  パフォーマンスの向上を期待するのであれば、必ず、クライアントのトランスポートを適切に構成しなければなりません。

`batchSend()` メソッドはレスポンスの配列を返します。その配列のキーは、リクエストの配列のキーに対応しています。
これによって、特定のリクエストに対するレスポンスを簡単に処理することが出来るようになっています。

```php
use yii\httpclient\Client;

$client = new Client();

$requests = [
    'news' => $client->get('http://domain.com/news'),
    'friends' => $client->get('http://domain.com/user/friends', ['userId' => 12]),
    'newComment' => $client->post('http://domain.com/user/comments', ['userId' => 12, 'content' => '新しいコメント']),
];
$responses = $client->batchSend($requests);

// `GET http://domain.com/news` の結果:
if ($responses['news']->isOk) {
    echo $responses['news']->content;
}

// `GET http://domain.com/user/friends` の結果:
if ($responses['friends']->isOk) {
    echo $responses['friends']->content;
}

// `POST http://domain.com/user/comments` の結果:
if ($responses['newComment']->isOk) {
    echo "コメントの追加が成功しました。";
}
```
