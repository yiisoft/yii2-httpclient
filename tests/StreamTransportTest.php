<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\StreamTransport;

/**
 * @group stream
 */
class StreamTransportTest extends TransportTestCase
{
    /**
     * @inheritdoc
     */
    protected function transport()
    {
        return StreamTransport::className();
    }
}