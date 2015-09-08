トランスポート
==============

[[\yii\httpclient\Client]] は、実際に HTTP メッセージを送信するいくつかの異なる方法、すなわち、いくつかのトランスポートをサポートしています。
事前定義されているトランスポートは以下のものです。

 - [[\yii\httpclient\StreamTransport]] - HTTP メッセージを送信するのに [Streams](http://php.net/manual/ja/book.stream.php) を使います。
   このトランスポートがデフォルトとして使用されます。
   これは、何らかの PHP 拡張を追加したり、ライブラリをインストールしたりすることを要求しませんが、バッチ送信のような高度な機能はサポートしません。
 - [[\yii\httpclient\CurlTransport]] - HTTP メッセージを送信するのに [Client URL ライブラリ (cURL)](http://php.net/manual/ja/book.curl.php) を使用します。
   このトランスポートは PHP 'curl' 拡張がインストールされていることを要求しますが、バッチ送信のような高度な機能に対するサポートを提供します。

特定のクライアントによって使用されるべきトランスポートを [[\yii\httpclient\Client::transport]] を使って構成することが出来ます。

```php
use yii\httpclient\Client;

$client = new Client([
    'transport' => 'yii\httpclient\CurlTransport'
]);
```


## カスタムトランスポートを作成する

メッセージの送信を独自の方法で行うあなた自身のトランスポートを作成することが出来ます。
そうするためには、[[\yii\httpclient\Transport]] クラスを拡張して、最低限、`send()` メソッドを実装しなければなりません。
必要なことは、HTTP レスポンスのコンテントとヘッダを決定することが全てです。
そうすれば、それらから [[\yii\httpclient\Client::createResponse()]] を使ってレスポンスオブジェクトを作成することが出来ます。

```php
use yii\httpclient\Transport;

class MyTransport extends Transport
{
    /**
     * @inheritdoc
     */
    public function send($request)
    {
        $responseContent = '???';
        $responseHeaders = ['???'];

        return $request->client->createResponse($responseContent, $responseHeaders);
    }
}
```

また、非同期の並列送信など、複数のリクエストを効率的に送信する方法がある場合は、`batchSend()` メソッドをオーバーライドすることも出来ます。
