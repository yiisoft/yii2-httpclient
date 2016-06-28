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

    /**
     * @inheritdoc
     */
    public function testFollowLocation()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('HHVM does not support `follow_location` option for stream');
        }

        parent::testFollowLocation();
    }
}