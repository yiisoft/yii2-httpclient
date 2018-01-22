HTTP Client DebugPanel ���g��
=============================

yii2 HTTP Client �G�N�X�e���V�����́Ayii �̃f�o�b�O���W���[���Ɠ����\�ȃf�o�b�O�p�l����񋟂��A���s���ꂽ HTTP ���N�G�X�g��\�����܂��B

���̃R�[�h�����Ȃ��̃A�v���P�[�V�����̍\�����ɒǉ������ DebugPanel ���L���ɂȂ�܂�
(���Ƀf�o�b�O���W���[����L���ɂ��Ă���ꍇ�́A�p�l���̍\������ǉ����邾���ŏ\���ł�)�B

```php
    // ...
    'bootstrap' => ['debug'],
    'modules' => [
        'debug' => [
            'class' => 'yii\\debug\\Module',
            'panels' => [
                'httpclient' => [
                    'class' => 'yii\\httpclient\\debug\\HttpClientPanel',
                ],
            ],
        ],
    ],
    // ...
```

���̃p�l���ɂ���āA���O�ɋL�^���ꂽ HTTP ���N�G�X�g�����s���āA���̃��X�|���X���m�F���邱�Ƃ��o���܂��B
���X�|���X�̓e�L�X�g�\���Ƃ��ĕ\�����邱�Ƃ��A�܂��A�u���E�U�ɒ��ړn�����Ƃ��o���܂��B

> Note: �f�o�b�O�p�l���ɂ���Ď��s�ł���̂́A���O�ɋL�^���ꂽ�ʏ�� HTTP ���N�G�X�g�����ł��B�o�b�`���M���ꂽ���N�G�X�g�͎��s�ł��܂���B
  �܂��A���O�ɋL�^���ꂽ���N�G�X�g�̃R���e���g�́A[[\yii\httpclient\Client::$contentLoggingMaxSize]] �ɏ]���Đ؂�l�߂��Ă��邩���m�ꂸ�A�]���āA���s�Ɏ��s������A�\�����Ȃ����ʂ𐶂����肷��ꍇ�����邱�Ƃ�S�ɗ��߂Ă����Ă��������B
