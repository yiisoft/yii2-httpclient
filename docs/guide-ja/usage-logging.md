���M���O�ƃv���t�@�C�����O
==========================

���̃G�N�X�e���V�����ł́A���M���ꂽ HTTP ���N�G�X�g�̃��M���O�ƁA���̎��s�̃v���t�@�C�����O���\�ł��B
���O�^�[�Q�b�g���Z�b�g�A�b�v���āAHTTP ���N�G�X�g�Ɋ֌W����S�ẴG���g����ߑ����邽�߂ɂ́A`yii\httpclient\Transport*` �Ƃ����J�e�S�����g�p���Ȃ���΂Ȃ�܂���B
�Ⴆ�΁A

```php
return [
    // ...
    'components' => [
        // ...
        'log' => [
            // ...
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/http-request.log',
                    'categories' => ['yii\httpclient\*'],
                ],
                // ...
            ],
        ],
    ],
];
```

[HTTP �N���C�A���g DebugPanel] ���g���đS�Ă̊֘A���郍�O�����邱�Ƃ��o���܂��B


> ����: HTTP ���N�G�X�g�̃R���e���g�ɂ͔��ɒ������̂����邽�߁A��������O�̒��ɑS���ۑ�����ƂȂ�ƁA���炩�̖�肪������\��������܂��B
  ���̂��߁A���O�ɋL�^����郊�N�G�X�g�R���e���g�̍ő咷�ɑ΂��ẮA�������݂����Ă��܂��B
  �R���e���g�̍ő咷�� [[\yii\httpclient\Client::$contentLoggingMaxSize]] �ɂ���Đ��䂳��A����𒴂���R���e���g�͂��ׂă��M���O�̑O�ɐ؂�l�߂��܂��B
