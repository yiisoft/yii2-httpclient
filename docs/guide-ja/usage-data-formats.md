�f�[�^�`��
==========

�f�[�^�`���� HTTP ���b�Z�[�W�̃R���e���g���쐬�܂��͉�͂�����@�����肵�܂��B
����������ƁA�f�[�^�`���ɂ���āA[[\yii\httpclient\Message::$data]] �� [[\yii\httpclient\Message::$content]] �����݂ɂǂ̂悤�ɕϊ������ׂ��������肳��܂��B

���̌`�����f�t�H���g�ŃT�|�[�g����Ă��܂��B

 - [[\yii\httpclient\Client::FORMAT_JSON]] - JSON �`��
 - [[\yii\httpclient\Client::FORMAT_URLENCODED]] - RFC1738 �ɂ���� urlencode ���ꂽ�N�G��������
 - [[\yii\httpclient\Client::FORMAT_RAW_URLENCODED]] - PHP_QUERY_RFC3986 �ɂ���� urlencode ���ꂽ�N�G��������
 - [[\yii\httpclient\Client::FORMAT_XML]] - XML �`��

���ꂼ��̌`���͓�̎��́A'formatter' �� 'parser' �ɂ���ăJ�o�[����܂��B
Formatter �́A���N�G�X�g�̃R���e���g���f�[�^����쐬�������@�����肵�܂��B
Parser �́A���̃��X�|���X�R���e���g���f�[�^�ɉ�͂������@�����肵�܂��B

[[\yii\httpclient\Client]] �́A��q�̌`�����ׂĂɂ��āA�����I�ɑΉ����� formatter �� parser ��I�����܂��B
�������A���̐U�镑���́A[[\yii\httpclient\Client::$formatters]] �� [[\yii\httpclient\Client::$parsers]] ���g���ĕύX���邱�Ƃ��o���܂��B
�����̃t�B�[���h�ɂ���āA���Ȃ����g�̌`����ǉ�������A�W���I�Ȍ`����ύX�����肷�邱�Ƃ��o���܂��B
�Ⴆ�΁A

```php
use yii\httpclient\Client;

$client = new Client([
    'formatters' => [
        'myformat' => 'app\components\http\MyFormatter', // �V���� formatter ��ǉ�
        Client::FORMAT_XML => 'app\components\http\MyXMLFormatter', // �f�t�H���g�� XML formatter ���I�[�o�[���C�h
    ],
]);
```

���Ȃ����g�� parser ���쐬����Ƃ��� [[\yii\httpclient\ParserInterface]] ���������Ȃ���΂Ȃ�܂���B
formatter �̏ꍇ��  [[\yii\httpclient\ParserInterface]] �ł��B
�Ⴆ�΁A

```php
use yii\httpclient\FormatterInterface;
use yii\httpclient\ParserInterface;
use yii\httpclient\Response;

class ParserIni implements ParserInterface
{
    public function parse(Response $response)
    {
        return parse_ini_string($response->content);
    }
}

class FormatterIni implements FormatterInterface
{
    public function format(Request $request)
    {
        $request->getHeaders()->set('Content-Type', 'text/ini   ; charset=UTF-8');

        $pairs = []
        foreach ($request->data as $name => $value) {
            $pairs[] = "$name=$value";
        }

        $request->setContent(implode("\n", $pairs));
        return $request;
    }
}
```
