<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\TransportStream;

class TransportStreamTest extends TransportTestCase
{
    /**
     * @inheritdoc
     */
    protected function transport()
    {
        return TransportStream::className();
    }
}