Multi-part content
==================

HTTP message content may consist of several parts with different content type. This is usually necessary
in case of a file upload request. You may compose a multi-part content using `addContent()`, `addFile()` or
`addFileContent()` methods of [[\yii\httpclient\Request]].
For example, if you wish to emulate file uploading via web form, you can use code like following:

```php
use yii\httpclient\Client;

$client = new Client();
$response = $client->createRequest()
    ->setMethod('post')
    ->setUrl('http://domain.com/file/upload')
    ->addFile('file', '/path/to/source/file.jpg')
    ->send();
```

If there is [[\yii\httpclient\Request::data]] specified, its values will be sent automatically as content parts
in case request is marked as multi-part one.
For example: assume we wish emulate submitting of the following form:

```html
<form name="profile-form" method="post" action="http://domain.com/user/profile" enctype="multipart/form-data">
    <input type="text" name="username" value="">
    <input type="text" name="email" value="">
    <input type="file" name="avatar">
    <!-- ... -->
</form>
```

you can do this using following code:

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
