Multipart-содержимое
==================

Содержимое HTTP-сообщения может состоять из нескольких частей с различным типом содержимого. Как правило, это необходимо 
в случае запроса загрузки файла. Вы можете составить multipart-содержимое, используя методы `addContent()`, `addFile()` или
`addFileContent()` из [[\yii\httpclient\Request]].
Например, если вы хотите эмулировать загрузку файлов через веб-форму, вы можете использовать следующий код:

```php
use yii\httpclient\Client;

$client = new Client();
$response = $client->createRequest()
    ->setMethod('POST')
    ->setUrl('http://domain.com/file/upload')
    ->addFile('file', '/path/to/source/file.jpg')
    ->send();
```

Если указано [[\yii\httpclient\Request::$data]], то значение будет отправляться в виде частей содержимого автоматически,
если запрос помечен, как многочастный.
Например: предположим, что необходимо эмулировать отправку следующей формы:

```html
<form name="profile-form" method="post" action="http://domain.com/user/profile" enctype="multipart/form-data">
    <input type="text" name="username" value="">
    <input type="text" name="email" value="">
    <input type="file" name="avatar">
    <!-- ... -->
</form>
```

Вы можете сделать это, используя следующий код:

```php
use yii\httpclient\Client;

$client = new Client();
$response = $client->createRequest()
    ->setMethod('POST')
    ->setUrl('http://domain.com/user/profile')
    ->setData([
        'username' => 'johndoe',
        'email' => 'johndoe@domain.com',
    ])
    ->addFile('avatar', '/path/to/source/image.jpg')
    ->send();
```

Имейте в виду, что добавление нескольких файлов с одинаковым именем приведет к тому, что они будут перезаписаны последним из них.
Вы должны самостоятельно контролировать индексы табличного ввода для добавляемых файлов, например:

```php
use yii\httpclient\Client;

$client = new Client();
$response = $client->createRequest()
    ->setMethod('POST')
    ->setUrl('http://domain.com/gallery')
    ->addFile('avatar[0]', '/path/to/source/image1.jpg')
    ->addFile('avatar[1]', '/path/to/source/image2.jpg')
    ...
    ->send();
```