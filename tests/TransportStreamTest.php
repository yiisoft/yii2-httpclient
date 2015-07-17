<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\TransportStream;

/**
 * @group stream
 */
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