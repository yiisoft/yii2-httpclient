Multi-part 内容（multipart/form-data）
====================================


HTTP 消息内容是由不同内容类型组成的。 比如文件上传。 可以使用 [[\yii\httpclient\Request]] 的 `addContent()` ， `addFile()` 或 `addFileContent()` 方法组成一个请求。
例如，模拟 Web 表单文件上传，可以使用如下代码：

```php
use yii\httpclient\Client;

$client = new Client();
$response = $client->createRequest()
    ->setMethod('post')
    ->setUrl('http://domain.com/file/upload')
    ->addFile('file', '/path/to/source/file.jpg')
    ->send();
```

如果指定了 [[\yii\httpclient\Request::data]] ，普通类型的内容和文件类型将被自动处理。
例如：模拟提交以下表单：

```html
<form name="profile-form" method="post" action="http://domain.com/user/profile" enctype="multipart/form-data">
    <input type="text" name="username" value="">
    <input type="text" name="email" value="">
    <input type="file" name="avatar">
    <!-- ... -->
</form>
```

通过如下代码实现:

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
