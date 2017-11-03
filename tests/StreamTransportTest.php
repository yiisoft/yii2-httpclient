<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\StreamTransport;

/**
 * @group stream
 */
class StreamTransportTest extends TransportTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function transport()
    {
        return StreamTransport::className();
    }

    /**
     * {@inheritdoc}
     */
    public function testFollowLocation()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('HHVM does not support `follow_location` option for stream');
        }

        parent::testFollowLocation();
    }

    public function testComposeContextOptions()
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
            'sslLocalCert' => '/path/to/client.crt',
            'sslLocalPk' => '/path/to/client.key',
            'sslPassphrase' => 'passphrase of client.crt',
        ];
        $contextOptions = $this->invoke($transport, 'composeContextOptions', [$options]);

        $expectedContextOptions = [
            'http' => [
                'protocol_version' => $options['protocolVersion'],
                'timeout' => $options['timeout'],
                'proxy' => $options['proxy'],
                'user_agent' => $options['userAgent'],
                'follow_location' => $options['followLocation'],
                'max_redirects' => $options['maxRedirects'],
            ],
            'ssl' => [
                'verify_peer' => $options['sslVerifyPeer'],
                'cafile' => $options['sslCafile'],
                'capath' => $options['sslCapath'],
                'local_cert' => $options['sslLocalCert'],
                'local_pk' => $options['sslLocalPk'],
                'passphrase' => $options['sslPassphrase'],
            ],
        ];
        $this->assertEquals($expectedContextOptions, $contextOptions);
    }
}