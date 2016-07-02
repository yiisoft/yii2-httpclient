��{�I�Ȏg�p���@
================

HTTP ���N�G�X�g�𑗐M���邽�߂ɂ́A[[\yii\httpclient\Client]] ���C���X�^���X�����āA���� `createRequest()`
���\�b�h���g���āAHTTP ���N�G�X�g���쐬����K�v������܂��B
���ɁA���Ȃ��̖ړI�ɏ]���ă��N�G�X�g�̑S�Ẵp�����[�^���\�����āA���N�G�X�g�𑗐M���܂��B
���ʂƂ��āA���Ȃ��́A���X�|���X�̑S�Ă̏��ƃf�[�^��ێ����� [[\yii\httpclient\Response]] �̃C���X�^���X���󂯎�邱�ƂɂȂ�܂��B
�Ⴆ�΁A

```php
use yii\httpclient\Client;

$client = new Client();
$response = $client->createRequest()
    ->setMethod('post')
    ->setUrl('http://example.com/api/1.0/users')
    ->setData(['name' => 'John Doe', 'email' => 'johndoe@example.com'])
    ->send();
if ($response->isOk) {
    $newUserId = $response->data['id'];
}
```

�V�������N�G�X�g�����������Ƃ�P�������邽�߂ɁA`get()`�A`post()`�A`put()` �Ȃǂ̃V���[�g�J�b�g���\�b�h���g���Ă��\���܂���B
����̃h���C���ɑ΂��ĕ����̃��N�G�X�g�𑗐M����ꍇ (�Ⴆ�� REST API �g�p����ꍇ) �́A
�P��� [[\yii\httpclient\Client]] �C���X�^���X���g���āA���� `baseUrl` �v���p�e�B�ɂ��̃h���C����ݒ肷�邱�Ƃ��o���܂��B
���̂悤�ɂ���ƁA�V�������N�G�X�g���쐬����Ƃ��ɁA���� URL �������w�肷�邱�Ƃ��o����悤�ɂȂ�܂��B
�]���āA���炩�� REST API �ɑ΂��鐔�̃��N�G�X�g�́A���L�̂悤�ɏ������Ƃ��o���܂��B

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'http://example.com/api/1.0']);

$newUserResponse = $client->post('users', ['name' => 'John Doe', 'email' => 'johndoe@example.com'])->send();
$articleResponse = $client->get('articles', ['name' => 'Yii 2.0'])->send();
$client->post('subscriptions', ['user_id' => $newUserResponse->data['id'], 'article_id' => $articleResponse->data['id']])->send();
```


## ���܂��܂ȃR���e���g�`�����g��

�f�t�H���g�ł́AHTTP ���N�G�X�g�f�[�^�� 'form-urlencoded'�A�Ⴆ�΁A`param1=value1&param2=value2` �Ƃ��đ��M����܂��B
����̓E�F�u�t�H�[���ł͈�ʓI�Ȍ`���ł����AREST API �ɂƂ��Ă͂����ł͂Ȃ��A�ʏ�̓R���e���g�� JSON �܂��� XML �̌`���ł��邱�Ƃ��v������܂��B
���N�G�X�g�R���e���g�Ɏg�p�����`���́A`format` �v���p�e�B�܂��� `setFormat()` ���\�b�h���g�p���Đݒ肷�邱�Ƃ��o���܂��B
���L�̌`�����T�|�[�g����Ă��܂��B

 - [[\yii\httpclient\Client::FORMAT_JSON]] - JSON �`��
 - [[\yii\httpclient\Client::FORMAT_URLENCODED]] - RFC1738 �ɂ���� urlencode ���ꂽ�N�G��������
 - [[\yii\httpclient\Client::FORMAT_RAW_URLENCODED]] - PHP_QUERY_RFC3986 �ɂ���� urlencode ���ꂽ�N�G��������
 - [[\yii\httpclient\Client::FORMAT_XML]] - XML �`��

�Ⴆ�΁A

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'http://example.com/api/1.0']);
$response = $client->createRequest()
    ->setFormat(Client::FORMAT_JSON)
    ->setUrl('articles/search')
    ->setData([
        'query_string' => 'Yii',
        'filter' => [
            'date' => ['>' => '2015-08-01']
        ],
    ])
    ->send();
```

���X�|���X�I�u�W�F�N�g�́A'Content-Type' �w�b�_�ƃR���e���g���̂Ɋ�Â��āA�R���e���g�`���������I�Ɍ��o���܂��B
�]���āA�قƂ�ǂ̏ꍇ���X�|���X�̌`�����w�肷��K�v�͂Ȃ��A�P���� `getData()` ���\�b�h�܂��� `data` �v���p�e�B���g���΁A���X�|���X����͂��邱�Ƃ��o���܂��B
��L�̗�̑����Ƃ��āA���X�|���X�f�[�^���擾����ɂ͎��̂悤�ɂ��邱�Ƃ��o���܂��B

```php
$responseData = $response->getData(); // �S�Ă̋L�����擾
count($response->data) // �L���̐����擾
$article = $response->data[0] // �ŏ��̋L�����擾
```


## ���̃R���e���g������

�N�����Ȃ��ɑ΂��ē������ꂽ�`���Ɉˑ����邱�Ƃ�����������̂ł͂���܂���B
HTTP ���N�G�X�g�ɐ��̃R���e���g���g�p���鎖���A���X�|���X�̐��̃R���e���g���������邱�Ƃ��\�ł��B
�Ⴆ�΁A

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'http://example.com/api/1.0']);
$response = $client->createRequest()
    ->setUrl('articles/search')
    ->addHeaders(['content-type' => 'application/json'])
    ->setContent('{query_string: "Yii"}')
    ->send();

echo 'Search results:<br>';
echo $response->content;
```

[[\yii\httpclient\Request]] �́A`content` ���ݒ肳��Ă��Ȃ��ꍇ�ɂ����A�w�肳�ꂽ `data` ���t�H�[�}�b�g���܂��B
[[\yii\httpclient\Response]] �́A`data` ��v�������ꍇ�ɂ����A`content` ����͂��܂��B


## ���N�G�X�g�ƃ��X�|���X�̃I�u�W�F�N�g�����O�ɍ\������

�������̎����悤�ȃ��N�G�X�g��P��� [[\yii\httpclient\Client]] �C���X�^���X���g���ď�������ꍇ�A�Ⴆ�� REST API �������悤�ȏꍇ�́A���N�G�X�g�ƃ��X�|���X�̃I�u�W�F�N�g�̂��߂ɂ��Ȃ����g�̍\������錾���邱�Ƃɂ���āA�R�[�h��P�������č��������邱�Ƃ��o���܂��B
���̂��߂ɂ́A[[\yii\httpclient\Client]] �� `requestConfig` ����� `responsConfig` �̃t�B�[���h���g�p���܂��B
�Ⴆ�΁A����̃N���C�A���g�ɂ���č쐬�����S�Ẵ��N�G�X�g�ɑ΂��� JSON �`�����Z�b�g�A�b�v�������ꍇ�́A���̂悤�ɂ��܂��B

```php
use yii\httpclient\Client;

$client = new Client([
    'baseUrl' => 'http://example.com/api/1.0',
    'requestConfig' => [
        'format' => Client::FORMAT_JSON
    ],
    'responseConfig' => [
        'format' => Client::FORMAT_JSON
    ],
]);

$request = $client->createRequest();
echo $request->format; // �o��: 'json'
```

> Tip: ���炩�̒ǉ��̋@�\�𗘗p���邽�߂ɁA�\�����z��� 'class' �L�[���g���āA���N�G�X�g�ƃ��X�|���X�̃I�u�W�F�N�g�ɂ��Ȃ����g�̃N���X���w�肷�邱�Ƃ��\�ł��B


## �w�b�_������

`setHeaders()` ���\�b�h�� `addHeaders()` ���\�b�h���g���āA���N�G�X�g�w�b�_���w�肷�邱�Ƃ��o���܂��B
�܂��A`getHeaders()` ���\�b�h�܂��� `headers` �v���p�e�B���g���ƁA���ɒ�`����Ă���w�b�_�� [[\yii\web\HeaderCollection]] �̃C���X�^���X�Ƃ��Ď擾���邱�Ƃ��o���܂��B
�Ⴆ�΁A

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'http://example.com/api/1.0']);
$request = $client->createRequest()
    ->setHeaders(['content-type' => 'application/json'])
    ->addHeaders(['user-agent' => 'My User Agent']);

$request->getHeaders()->add('accept-language', 'en-US;en');
$request->headers->set('user-agent', 'User agent override');
```

���X�|���X�I�u�W�F�N�g���擾������́A`getHeaders()` ���\�b�h�܂��� `headers` �v���p�e�B���g���āA���ׂẴ��X�|���X�w�b�_�ɃA�N�Z�X���邱�Ƃ��o���܂��B

```php
$response = $request->send();
echo $response->getHeaders()->get('content-type');
echo $response->headers->get('content-encoding');
```


## �N�b�L�[������

�N�b�L�[�̓w�b�_�̒l�Ƃ��đ���M����邾���̂��̂ł����A[[\yii\httpclient\Request]] �� [[\yii\httpclient\Request]] �́A[[\yii\web\Cookie]] ����� [[\yii\web\CookieCollection]] ���g���ăN�b�L�[���������߂̓Ɨ������C���^�[�t�F�C�X��񋟂��Ă��܂��B

���N�G�X�g�̃N�b�L�[�� `setCookies()` �܂��� `addCookies()` ���\�b�h�Ŏw�肷�邱�Ƃ��o���܂��B
�܂��A`getCookies()` ���\�b�h�܂��� `cookies` �v���p�e�B���g���ƁA���ɒ�`����Ă���N�b�L�[�� [[\yii\web\CookieCollection]] �̃C���X�^���X�Ƃ��Ď擾���邱�Ƃ��o���܂��B
�Ⴆ�΁A

```php
use yii\httpclient\Client;
use yii\web\Cookie;

$client = new Client(['baseUrl' => 'http://example.com/api/1.0']);
$request = $client->createRequest()
    ->setCookies([
        ['name' => 'country', 'value' => 'USA'],
        new Cookie(['name' => 'language', 'value' => 'en-US']),
    ])
    ->addCookies([
        ['name' => 'view_mode', 'value' => 'full']
    ]);

$request->cookies->add(['name' => 'display-notification', 'value' => '0']);
```

���X�|���X�I�u�W�F�N�g���擾������́A`getCookies()` ���\�b�h�܂��� `cookies` �v���p�e�B���g���āA���X�|���X�̃N�b�L�[�S�ĂɃA�N�Z�X���邱�Ƃ��o���܂��B

```php
$response = $request->send();
echo $response->getCookies()->get('country');
echo $response->headers->get('PHPSESSID');
```

�P���ȃR�s�[���g���āA���X�|���X�I�u�W�F�N�g���烊�N�G�X�g�I�u�W�F�N�g�ɃN�b�L�[��]�����邱�Ƃ��o���܂��B
�Ⴆ�΁A�����̃E�F�u�A�v���P�[�V�����Ń��[�U�̃v���t�@�C����ҏW����K�v������Ƃ��܂��傤�B
���[�U�̃v���t�@�C���̓��O�C����ɂ̂݃A�N�Z�X�ł��܂��̂ŁA�ŏ��Ƀ��O�C�����āA�����Ő������ꂽ�Z�b�V�������g���čX�ɍ�Ƃ����܂��B

```php
use yii\httpclient\Client;

$client = new Client(['baseUrl' => 'http://example.com']);

$loginResponse = $client->post('login', [
    'username' => 'johndoe',
    'password' => 'somepassword',
])->send();

// $loginResponse->cookies->get('PHPSESSID') ���V�����Z�b�V���� ID ��ێ����Ă���

$client->post('account/profile', ['birthDate' => '10/11/1982'])
    ->setCookies($loginResponse->cookies) // ���X�|���X�̃N�b�L�[�����N�G�X�g�̃N�b�L�[�ɓ]��
    ->send();
```
