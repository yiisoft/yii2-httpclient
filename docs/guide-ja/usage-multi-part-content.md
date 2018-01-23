マルチパートコンテント
======================

HTTP のメッセージコンテントは、コンテントタイプの異なるいくつかの部分から成る場合があります。
通常、ファイルのアップロードをリクエストする場合に、それが必要になります。
[[\yii\httpclient\Request]] の `addContent()`、`addFile()` または`addFileContent()` メソッドを使って、マルチパートのコンテントを作成することが出来ます。
例えば、ウェブフォームを使うファイルのアップロードをエミュレートしたい場合は、次のようなコードを使用する事が出来ます。

```php
use yii\httpclient\Client;

$client = new Client();
$response = $client->createRequest()
    ->setMethod('post')
    ->setUrl('http://domain.com/file/upload')
    ->addFile('file', '/path/to/source/file.jpg')
    ->send();
```

リクエストがマルチパートであるとマークされている場合であっても、[[\yii\httpclient\Request::data]] が指定されている場合は、その値がコンテントの一部として自動的に送信されます。
例えば、次のようなフォームの送信をエミュレートしたいと仮定しましょう。
```html
<form name="profile-form" method="post" action="http://domain.com/user/profile" enctype="multipart/form-data">
    <input type="text" name="username" value="">
    <input type="text" name="email" value="">
    <input type="file" name="avatar">
    <!-- ... -->
</form>
```

これは、次のようなコードを使って実行することが出来ます。

```php
use yii\httpclient\Client;

$client = new Client();
$response = $client->createRequest()
    ->setMethod('post')
    ->setUrl('http://domain.com/user/profile')
    ->setData([
        'username' => 'johndoe',
        'email' => 'johndoe@domain.com',
    ])
    ->addFile('avatar', '/path/to/source/image.jpg')
    ->send();
```
