<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\Client;
use yii\httpclient\CurlTransport;

/**
 * @group curl
 */
class CurlTransportTest extends TransportTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function transport()
    {
        return CurlTransport::className();
    }

    /**
     * {@inheritdoc}
     */
    public function testCustomSslCertificate()
    {
        $this->markTestSkipped('Unable to test custom SSL certificate since CURL is too strict checking it.');
        //parent::testCustomSslCertificate();
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
            'sslLocalCert' => '/path/to/client.crt',
            'sslLocalPk' => '/path/to/client.key',
            'sslPassphrase' => 'passphrase of client.crt',
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
            CURLOPT_SSLCERT => $options['sslLocalCert'],
            CURLOPT_SSLKEY => $options['sslLocalPk'],
            CURLOPT_SSLCERTPASSWD => $options['sslPassphrase'],
        ];
        $this->assertEquals($expectedContextOptions, $contextOptions);
    }

    public function testPreparePostRequestWithEmptyBody()
    {
        $client = new Client([
            'transport' => 'yii\httpclient\CurlTransport',
        ]);
        $request = $client->createRequest();
        $request->setMethod('POST');
        $request->setUrl('http://app.test/full/url');

        $transport = $this->createClient()->getTransport();
        $curlOptions = $this->invoke($transport, 'prepare', [$request]);

        $expectedCurlOptions = [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => null,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => 'http://app.test/full/url',
            CURLOPT_HTTPHEADER => [
                0 => 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
                1 => 'Content-Length: 0',
            ],
        ];

        $this->assertEquals($expectedCurlOptions, $curlOptions);
    }

    public function testPrepareHeadRequestShouldNotHaveBody()
    {
        $client = new Client([
            'transport' => 'yii\httpclient\CurlTransport',
        ]);
        $request = $client->createRequest();
        $request->setMethod('HEAD');
        $request->setUrl('http://app.test/full/url');

        $transport = $this->createClient()->getTransport();
        $curlOptions = $this->invoke($transport, 'prepare', [$request]);

        $expectedCurlOptions = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => 'http://app.test/full/url',
            CURLOPT_HTTPHEADER => [
                0 => 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
                1 => 'Content-Length: 0',
            ],
            CURLOPT_CUSTOMREQUEST => 'HEAD',
            CURLOPT_NOBODY => true,
        ];

        $this->assertEquals($expectedCurlOptions, $curlOptions);
    }
}