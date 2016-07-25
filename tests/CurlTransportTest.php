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

    public function testComposeCurlOptions()
    {
        $transport = $this->createClient()->getTransport();

        $options = [
            'protocolVersion' => '1.1',
            'timeout' => 12,
            'proxy' => 'tcp://proxy.example.com:5100',
            'userAgent' => 'Test User Agent',
            'followLocation' => true,
            'maxRedirects' => 11,
            'sslVerifyPeer' => true,
            'sslCafile' => '/path/to/some/file',
            'sslCapath' => '/some/path',
        ];
        $contextOptions = $this->invoke($transport, 'composeCurlOptions', [$options]);

        $expectedContextOptions = [
            CURLOPT_HTTP_VERSION => $options['protocolVersion'],
            CURLOPT_TIMEOUT => $options['timeout'],
            CURLOPT_PROXY => $options['proxy'],
            CURLOPT_USERAGENT => $options['userAgent'],
            CURLOPT_FOLLOWLOCATION => $options['followLocation'],
            CURLOPT_MAXREDIRS => $options['maxRedirects'],
            CURLOPT_SSL_VERIFYPEER => $options['sslVerifyPeer'],
            CURLOPT_CAINFO => $options['sslCafile'],
            CURLOPT_CAPATH => $options['sslCapath'],
        ];
        $this->assertEquals($expectedContextOptions, $contextOptions);
    }
}