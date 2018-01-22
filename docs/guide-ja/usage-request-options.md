���N�G�X�g�̃I�v�V����
======================

[[\yii\httpclient\Request::$options]] ���g���āA����̃��N�G�X�g�̎��s�𒲐����邱�Ƃ��o���܂��B
�ȉ��̃I�v�V�������T�|�[�g����Ă��܂��B
 - timeout: integer�A���N�G�X�g�̎��s�ɋ��e�����ő�b���B
 - proxy: string�A�v���L�V�T�[�o�̃A�h���X���w�肷�� URI (�Ⴆ�΁Atcp://proxy.example.com:5100)�B
 - userAgent: string�AHTTP ���N�G�X�g�Ɏg�p����� "User-Agent: " �w�b�_�̓��e�B
 - followLocation: boolean�A�T�[�o�� HTTP �w�b�_�̈ꕔ�Ƃ��đ��M���邷�ׂĂ� "Location:" �w�b�_�ɏ]�����ۂ��B
 - maxRedirects: integer�Aredirect �ɏ]���ő�񐔁B
 - sslVerifyPeer: boolean�Apeer �̏ؖ����̌��؂����邩�ۂ��B
 - sslCafile: string�A���[�J���̃t�@�C���V�X�e����� Certificate Authority (CA) �t�@�C���̏ꏊ�B'sslVerifyPeer' �I�v�V�����ɂ���ă����[�g�� peer �� identity ��F�؂���ۂɂ��� CA �t�@�C����p����B
 - sslCapath: string�A������ CA �ؖ�����ێ�����f�B���N�g���B

�Ⴆ�΁A

```php
use yii\httpclient\Client;

$client = new Client();

$response = $client->createRequest()
    ->setMethod('POST')
    ->setUrl('http://domain.com/api/1.0/users')
    ->setData(['name' => 'John Doe', 'email' => 'johndoe@domain.com'])
    ->setOptions([
        'proxy' => 'tcp://proxy.example.com:5100', // �v���L�V���g�p
        'timeout' => 5, // �T�[�o���������Ȃ��ꍇ�̂��߂� 5 �b�̃^�C���A�E�g��ݒ�
    ])
    ->send();
```

> Tip: �f�t�H���g�̃��N�G�X�g�I�v�V������ [[\yii\httpclient\Client::$requestConfig]] �ɂ���Đݒ肷�邱�Ƃ��o���܂��B
  ���̏ꍇ�A���ʂȃ��N�G�X�g�I�v�V������ǉ��������Ƃ��́A�ݒ�ς݂̃I�v�V������ێ����邽�߂� [[\yii\httpclient\Request::addOptions()]] ���g���Ă��������B

����̃��N�G�X�g�̃g�����X�|�[�g�ɑ΂��Ă̂ݓK�p����I�v�V������n�����Ƃ��o���܂��B
[[\yii\httpclient\CurlTransport]] ���g���ꍇ�́A�ʏ�A���̂悤�ɂ��܂��B
�Ⴆ�΁A�ڑ��ƃf�[�^��M�ɂ��āAPHP cURL ���C�u�����ɂ���ăT�|�[�g����Ă���悤�ɁA�ʂ̃^�C���A�E�g���w�肵�����ł��傤�B
���̂悤�ɂ��āA�������邱�Ƃ��o���܂��B

```php
use yii\httpclient\Client;

$client = new Client([
    'transport' => 'yii\httpclient\CurlTransport' // �����Ŏg���I�v�V������ cURL �������T�|�[�g���Ă���
]);

$response = $client->createRequest()
    ->setMethod('POST')
    ->setUrl('http://domain.com/api/1.0/users')
    ->setData(['name' => 'John Doe', 'email' => 'johndoe@domain.com'])
    ->setOptions([
        CURLOPT_CONNECTTIMEOUT => 5, // �ڑ��^�C���A�E�g
        CURLOPT_TIMEOUT => 10, // �f�[�^��M�^�C���A�E�g
    ])
    ->send();
```

�ŗL�̃I�v�V�����̃T�|�[�g�ɂ��ẮA�ʂ̃g�����X�|�[�g�N���X�̃h�L�������g���Q�Ƃ��Ă��������B