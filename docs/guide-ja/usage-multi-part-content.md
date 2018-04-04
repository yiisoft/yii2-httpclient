�}���`�E�p�[�g�E�R���e���g
==========================

HTTP �̃��b�Z�[�W�E�R���e���g�́A�R���e���g�E�^�C�v�̈قȂ邢�����̕������琬��ꍇ������܂��B
�ʏ�A�t�@�C���̃A�b�v���[�h�����N�G�X�g����ꍇ�ɁA���ꂪ�K�v�ɂȂ�܂��B
[[\yii\httpclient\Request]] �� `addContent()`�A`addFile()` �܂���`addFileContent()` ���\�b�h���g���āA�}���`�E�p�[�g�̃R���e���g���쐬���邱�Ƃ��o���܂��B
�Ⴆ�΁A�E�F�u�E�t�H�[�����g���t�@�C���̃A�b�v���[�h���G�~�����[�g�������ꍇ�́A���̂悤�ȃR�[�h���g�p���鎖���o���܂��B

```php
use yii\httpclient\Client;

$client = new Client();
$response = $client->createRequest()
    ->setMethod('post')
    ->setUrl('http://domain.com/file/upload')
    ->addFile('file', '/path/to/source/file.jpg')
    ->send();
```

���N�G�X�g���}���`�E�p�[�g�ł���ƃ}�[�N����Ă���ꍇ�ł����Ă��A[[\yii\httpclient\Request::data]] ���w�肳��Ă���ꍇ�́A���̒l���R���e���g�̈ꕔ�Ƃ��Ď����I�ɑ��M����܂��B
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
    ->setMethod('post')
    ->setUrl('http://domain.com/user/profile')
    ->setData([
        'username' => 'johndoe',
        'email' => 'johndoe@domain.com',
    ])
    ->addFile('avatar', '/path/to/source/image.jpg')
    ->send();
```
