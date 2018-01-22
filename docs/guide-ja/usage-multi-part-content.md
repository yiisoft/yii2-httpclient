�}���`�p�[�g�R���e���g
======================

HTTP �̃��b�Z�[�W�R���e���g�́A�R���e���g�^�C�v�̈قȂ邢�����̕������琬��ꍇ������܂��B
�ʏ�A�t�@�C���̃A�b�v���[�h�����N�G�X�g����ꍇ�ɁA���ꂪ�K�v�ɂȂ�܂��B
[[\yii\httpclient\Request]] �� `addContent()`�A`addFile()` �܂���`addFileContent()` ���\�b�h���g���āA�}���`�p�[�g�̃R���e���g���쐬���邱�Ƃ��o���܂��B
�Ⴆ�΁A�E�F�u�t�H�[�����g���t�@�C���̃A�b�v���[�h���G�~�����[�g�������ꍇ�́A���̂悤�ȃR�[�h���g�p���鎖���o���܂��B

```php
use yii\httpclient\Client;

$client = new Client();
$response = $client->createRequest()
    ->setMethod('POST')
    ->setUrl('http://domain.com/file/upload')
    ->addFile('file', '/path/to/source/file.jpg')
    ->send();
```

���N�G�X�g���}���`�p�[�g�ł���ƃ}�[�N����Ă���ꍇ�ł����Ă��A[[\yii\httpclient\Request::$data]] ���w�肳��Ă���ꍇ�́A���̒l���R���e���g�̈ꕔ�Ƃ��Ď����I�ɑ��M����܂��B
�Ⴆ�΁A���̂悤�ȃt�H�[���̑��M���G�~�����[�g�������Ɖ��肵�܂��傤�B
```html
<form name="profile-form" method="post" action="http://domain.com/user/profile" enctype="multipart/form-data">
    <input type="text" name="username" value="">
    <input type="text" name="email" value="">
    <input type="file" name="avatar">
    <!-- ... -->
</form>
```

����́A���̂悤�ȃR�[�h���g���Ď��s���邱�Ƃ��o���܂��B

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