<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\CurlTransport;

/**
 * @group curl
 */
class CurlTransportTest extends TransportTestCase
{
    /**
     * @inheritdoc
     */
    protected function transport()
    {
        return CurlTransport::className();
    }
}