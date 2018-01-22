�g�����X�|�[�g
==============

[[\yii\httpclient\Client]] �́A���ۂ� HTTP ���b�Z�[�W�𑗐M���邢�����̈قȂ���@�A���Ȃ킿�A�������̃g�����X�|�[�g���T�|�[�g���Ă��܂��B
���O��`����Ă���g�����X�|�[�g�͈ȉ��̂��̂ł��B

 - [[\yii\httpclient\StreamTransport]] - HTTP ���b�Z�[�W�𑗐M����̂� [Streams](http://php.net/manual/ja/book.stream.php) ���g���܂��B
   ���̃g�����X�|�[�g���f�t�H���g�Ƃ��Ďg�p����܂��B
   ����́A���炩�� PHP �g����ǉ�������A���C�u�������C���X�g�[�������肷�邱�Ƃ�v�����܂��񂪁A�o�b�`���M�̂悤�ȍ��x�ȋ@�\�̓T�|�[�g���܂���B
 - [[\yii\httpclient\CurlTransport]] - HTTP ���b�Z�[�W�𑗐M����̂� [Client URL ���C�u���� (cURL)](http://php.net/manual/ja/book.curl.php) ���g�p���܂��B
   ���̃g�����X�|�[�g�� PHP 'curl' �g�����C���X�g�[������Ă��邱�Ƃ�v�����܂����A�o�b�`���M�̂悤�ȍ��x�ȋ@�\�ɑ΂���T�|�[�g��񋟂��܂��B

����̃N���C�A���g�ɂ���Ďg�p�����ׂ��g�����X�|�[�g�� [[\yii\httpclient\Client::$transport]] ���g���č\�����邱�Ƃ��o���܂��B

```php
use yii\httpclient\Client;

$client = new Client([
    'transport' => 'yii\httpclient\CurlTransport'
]);
```


## �J�X�^���g�����X�|�[�g���쐬����

���b�Z�[�W�̑��M��Ǝ��̕��@�ōs�����Ȃ����g�̃g�����X�|�[�g���쐬���邱�Ƃ��o���܂��B
�������邽�߂ɂ́A[[\yii\httpclient\Transport]] �N���X���g�����āA�Œ���A`send()` ���\�b�h���������Ȃ���΂Ȃ�܂���B
�K�v�Ȃ��Ƃ́AHTTP ���X�|���X�̃R���e���g�ƃw�b�_�����肷�邱�Ƃ��S�Ăł��B
��������΁A����炩�� [[\yii\httpclient\Client::createResponse()]] ���g���ă��X�|���X�I�u�W�F�N�g���쐬���邱�Ƃ��o���܂��B

```php
use yii\httpclient\Transport;

class MyTransport extends Transport
{
    /**
     * @inheritdoc
     */
    public function send($request)
    {
        $responseContent = '???';
        $responseHeaders = ['???'];

        return $request->client->createResponse($responseContent, $responseHeaders);
    }
}
```

�܂��A�񓯊��̕��񑗐M�ȂǁA�����̃��N�G�X�g�������I�ɑ��M������@������ꍇ�́A`batchSend()` ���\�b�h���I�[�o�[���C�h���邱�Ƃ��o���܂��B
