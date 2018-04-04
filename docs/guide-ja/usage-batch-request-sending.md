�o�b�`�E���N�G�X�g���M
======================

HTTP �N���C�A���g�́A[[\yii\httpclient\Client::batchSend()]] ���\�b�h���g���āA�����̃��N�G�X�g����x�ɑ��M���邱�Ƃ��\�ɂ��Ă��܂��B

```php
use yii\httpclient\Client;

$client = new Client();

$requests = [
    $client->get('http://domain.com/keep-alive'),
    $client->post('http://domain.com/notify', ['userId' => 12]),
];
$responses = $client->batchSend($requests);
```

[�g�����X�|�[�g](usage-transports.md) �ɂ���ẮA���̃��\�b�h���g�����Ƃɂ��A�p�t�H�[�}���X�����コ����Ƃ������_�𓾂邱�Ƃ��o���܂��B
�����̃g�����X�|�[�g�̒��ł́A[[\yii\httpclient\CurlTransport]] �݂̂�����ɓ��Ă͂܂�܂��B
����̓��N�G�X�g����񉻂��đ��M���邱�Ƃ��o���A����ɂ���ăv���O�����̎��s���Ԃ�Z�����邱�Ƃ��o���܂��B

> Note: `batchSend()` �ɂ����āA���N�G�X�g�����ȕ��@�ŏ������ĉ��炩�̗��_�𓾂邱�Ƃ��o����̂́A�������̓���̃g�����X�|�[�g�Ɍ����Ă��܂��B
  �f�t�H���g�ł́A�g�����X�|�[�g�́A�G���[���x���������邱�Ƃ͂���܂��񂪁A���N�G�X�g��������Ԃɑ��M���邾���ł��B
  �p�t�H�[�}���X�̌�������҂���̂ł���΁A�K���A�N���C�A���g�̃g�����X�|�[�g��K�؂ɍ\�����Ȃ���΂Ȃ�܂���B

`batchSend()` ���\�b�h�̓��X�|���X�̔z���Ԃ��܂��B���̔z��̃L�[�́A���N�G�X�g�̔z��̃L�[�ɑΉ����Ă��܂��B
����ɂ���āA����̃��N�G�X�g�ɑ΂��郌�X�|���X���ȒP�ɏ������邱�Ƃ��o����悤�ɂȂ��Ă��܂��B

```php
use yii\httpclient\Client;

$client = new Client();

$requests = [
    'news' => $client->get('http://domain.com/news'),
    'friends' => $client->get('http://domain.com/user/friends', ['userId' => 12]),
    'newComment' => $client->post('http://domain.com/user/comments', ['userId' => 12, 'content' => '�V�����R�����g']),
];
$responses = $client->batchSend($requests);

// `GET http://domain.com/news` �̌���:
if ($responses['news']->isOk) {
    echo $responses['news']->content;
}

// `GET http://domain.com/user/friends` �̌���:
if ($responses['friends']->isOk) {
    echo $responses['friends']->content;
}

// `POST http://domain.com/user/comments` �̌���:
if ($responses['newComment']->isOk) {
    echo "�R�����g�̒ǉ����������܂����B";
}
```
