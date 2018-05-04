リクエストのオプション
======================

[[\yii\httpclient\Request::options]] を使って、特定のリクエストの実行を調整することが出来ます。
以下のオプションがサポートされています。
 - timeout: integer、リクエストの実行に許容される最大秒数。
 - proxy: string、プロキシ・サーバのアドレスを指定する URI (例えば、tcp://proxy.example.com:5100)。
 - userAgent: string、HTTP リクエストに使用される "User-Agent: " ヘッダの内容。
 - followLocation: boolean、サーバが HTTP ヘッダの一部として送信するすべての "Location:" ヘッダに従うか否か。
 - maxRedirects: integer、redirect に従う最大回数。
 - sslVerifyPeer: boolean、peer の証明書の検証をするか否か。
 - sslCafile: string、ローカルのファイル・システム上の Certificate Authority (CA) ファイルの場所。
   'sslVerifyPeer' オプションによってリモートの peer の identity を認証する際にこの CA ファイルを用います。
 - sslCapath: string、複数の CA 証明書を保持するディレクトリ。
 - sslLocalCert: ローカルのファイル・システム上の証明書ファイルへのパス。これは、あなたの証明書とプライベート・キーを含む PEM エンコードされたファイルでなければなりません。オプションとして発行者の証明書チェーンを含むことが出来ます。プライベート・キーは sslLocakPk で指定された独立のファイルに入れておくことも出来ます。
 - sslLocalPk: 証明書 (sslLocalCert) とプライベート・キーのために独立したファイルを使う場合、ローカルのファイル・システム上のプライベート・キー・ファイルへのパス。
 - sslPassphrase: sslLocalCert ファイルのエンコードに使われたパスフレーズ。

例えば、

```php
use yii\httpclient\Client;

$client = new Client();

$response = $client->createRequest()
    ->setMethod('post')
    ->setUrl('http://domain.com/api/1.0/users')
    ->setData(['name' => 'John Doe', 'email' => 'johndoe@domain.com'])
    ->setOptions([
        'proxy' => 'tcp://proxy.example.com:5100', // プロキシを使用
        'timeout' => 5, // サーバが応答しない場合のために 5 秒のタイムアウトを設定
    ])
    ->send();
```

> Tip: デフォルトのリクエスト・オプションを [[\yii\httpclient\Client::requestConfig]] によって設定することが出来ます。
  その場合、特別なリクエスト・オプションを追加したいときは、設定済みのオプションを保持するために 
  [[\yii\httpclient\Request::addOptions()]] を使ってください。

特定のリクエストのトランスポートに対してのみ適用するオプションを渡すことも出来ます。[[\yii\httpclient\CurlTransport]] を使う場合は、通常、そのようにします。
例えば、接続とデータ受信について、PHP cURL ライブラリによってサポートされているように、個別のタイムアウトを指定したいでしょう。
次のようにして、そうすることが出来ます。

```php
use yii\httpclient\Client;

$client = new Client([
    'transport' => 'yii\httpclient\CurlTransport' // ここで使うオプションは cURL だけがサポートしている
]);

$response = $client->createRequest()
    ->setMethod('post')
    ->setUrl('http://domain.com/api/1.0/users')
    ->setData(['name' => 'John Doe', 'email' => 'johndoe@domain.com'])
    ->setOptions([
        CURLOPT_CONNECTTIMEOUT => 5, // 接続タイムアウト
        CURLOPT_TIMEOUT => 10, // データ受信タイムアウト
    ])
    ->send();
```

固有のオプションのサポートについては、個別のトランスポート・クラスのドキュメントを参照してください。