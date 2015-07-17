<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\TransportCurl;

/**
 * @group curl
 */
class TransportCurlTest extends TransportTestCase
{
    /**
     * @inheritdoc
     */
    protected function transport()
    {
        return TransportCurl::className();
    }
}