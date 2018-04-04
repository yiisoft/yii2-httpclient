�N���C�A���g�̃C���X�^���X���Z�b�g�A�b�v����
============================================

���̃G�N�X�e���V�����̎g�p�́A[[\yii\httpclient\Client]] �I�u�W�F�N�g���C���X�^���X������Ƃ��납��n�܂�܂��B
[[\yii\httpclient\Client]] �����Ȃ��̃v���O�����ɓ���������@�͂���������܂��B
�����ł́A�����Ƃ���ʓI�ȃA�v���[�`��������܂��B


## �N���C�A���g���A�v���P�[�V�����E�R���|�[�l���g�Ƃ��ăZ�b�g�A�b�v����

[[\yii\httpclient\Client]] �� [[\yii\base\Component]] �̊g���ł��̂ŁA[[\yii\di\Container]] �̃��x���ŁA���Ȃ킿�A�A�v���P�[�V�����E�R���|�[�l���g�Ƃ��āA�Z�b�g�A�b�v���邱�Ƃ��o���܂��B
�Ⴆ�΁A

```php
return [
    // ...
    'components' => [
        // ...
        'phpNetHttp' => [
            'class' => 'yii\httpclient\Client',
            'baseUrl' => 'http://uk.php.net',
        ],
    ],
];

// ...
echo Yii::$app->phpNetHttp->get('docs.php')->send()->content;
```


## �N���C�A���g�E�N���X���g������

[[\yii\httpclient\Client]] �̓A�v���P�[�V�����E�R���|�[�l���g�Ƃ��Ďg�p���鎖���o���邽�߁A�P�ɂ�����g�����āA���Ȃ����K�v�Ƃ��鉽�炩�̃J�X�^���E���W�b�N��ǉ����邱�Ƃ��o���܂��B

```php
use yii\httpclient\Client;

class MyRestApi extends Client
{
    public $baseUrl = 'http://my.rest.api/';

    public function addUser(array $data)
    {
        $response = $this->post('users', $data)->send();
        if (!$response->isOk) {
            throw new \Exception('���[�U��ǉ����邱�Ƃ��o���܂���B');
        }
        return $response->data['id'];
    }

    // ...
}
```


## �N���C�A���g�E�I�u�W�F�N�g�����b�v����

[[\yii\httpclient\Client]] �̃C���X�^���X���R���|�[�l���g�̓����t�B�[���h�Ƃ��Ďg�p���āA�����̕��G�ȋ@�\��񋟂����鎖���o���܂��B
�Ⴆ�΁A

```php
use yii\base\Component;
use yii\httpclient\Client;

class MyRestApi extends Component
{
    public $baseUrl = 'http://my.rest.api/';

    private $_httpClient;

    public function getHttpClient()
    {
        if (!is_object($this->_httpClient)) {
            $this->_httpClient = Yii::createObject([
                'class' => Client::className(),
                'baseUrl' => $this->baseUrl,
            ]);
        }
        return $this->_httpClient;
    }

    public function addUser(array $data)
    {
        $response = $this->getHttpClient()->post('users', $data)->send();
        if (!$response->isOk) {
            throw new \Exception('���[�U��ǉ����邱�Ƃ��o���܂���B');
        }
        return $response->data['id'];
    }

    // ...
}
```
