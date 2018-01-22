Treść wieloczęściowa
====================

Treść wiadomości HTTP może składać się z kilku części o różnych typach. Taka struktura jest zwykle wymagana w przypadku 
przesyłania plików. Wieloczęściową treść można skomponować za pomocą metod `addContent()`, `addFile()` i `addFileContent()` 
klasy [[\yii\httpclient\Request]].
Przykładowo, aby zasymulować przesyłanie plików za pomocą formularza web, można użyć następującego kodu:

```php
use yii\httpclient\Client;

$client = new Client();
$response = $client->createRequest()
    ->setMethod('POST')
    ->setUrl('http://domain.com/file/upload')
    ->addFile('file', '/path/to/source/file.jpg')
    ->send();
```

Jeśli określono właściwość [[\yii\httpclient\Request::$data]], jej wartości zostaną automatycznie przesłane jako części
treści w przypadku, gdy żądanie jest oznaczone jako wieloczęściowe.
Przykładowo, aby zasymulować wysłanie następującego formularza:

```html
<form name="profile-form" method="post" action="http://domain.com/user/profile" enctype="multipart/form-data">
    <input type="text" name="username" value="">
    <input type="text" name="email" value="">
    <input type="file" name="avatar">
    <!-- ... -->
</form>
```

można użyć poniższego kodu:

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

Note that attaching multiple files with the same name will cause them to be overridden by the latest one.
You have to control possible tabular input indexes for the attached files on your own, for example:

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