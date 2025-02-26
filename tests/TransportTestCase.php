<?php

namespace yiiunit\extensions\httpclient;

use Yii;
use yii\helpers\FileHelper;
use yii\httpclient\Client;
use yii\httpclient\Request;
use yii\httpclient\RequestEvent;
use yii\httpclient\Response;

/**
 * This is the base class for HTTP message transport unit tests.
 */
abstract class TransportTestCase extends TestCase
{
    protected function setUp(): void
    {
        $this->mockApplication();
    }

    /**
     * @return mixed transport configuration.
     */
    abstract protected function transport();

    /**
     * @return Client http client instance
     */
    protected function createClient()
    {
        return new Client(['transport' => $this->transport()]);
    }

    private function assertResponseIsOK(Response $response)
    {
        $this->assertTrue($response->getIsOk(), 'Response code was not OK but ' . $response->getStatusCode() . ': ' . $response->getContent());
    }

    public function testSend()
    {
        $client = $this->createClient();
        $client->baseUrl = 'https://www.php.net/';
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('docs.php')
            ->send();

        $this->assertResponseIsOK($response);
        $content = $response->getContent();
        $this->assertNotEmpty($content);
        $this->assertStringContainsString('<h1>Documentation</h1>', $content);
    }

    /**
     * @depends testSend
     */
    public function testSendPost()
    {
        $client = $this->createClient();
        $client->baseUrl = 'https://www.php.net/';
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('search.php')
            ->setData(['pattern' => 'curl'])
            ->send();
        $this->assertResponseIsOK($response);
    }

    /**
     * @depends testSend
     */
    public function testBatchSend()
    {
        $client = $this->createClient();
        $client->baseUrl = 'https://www.php.net/';

        $requests = [];
        $requests['docs'] = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('docs.php');
        $requests['support'] = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('support.php');

        $responses = $client->batchSend($requests);
        $this->assertCount(count($requests), $responses);

        foreach ($responses as $name => $response) {
            $this->assertResponseIsOK($response);
        }

        $this->assertInstanceOf(Response::class, $responses['docs']);
        $this->assertInstanceOf(Response::class, $responses['support']);
        $this->assertStringContainsString('<h1>Documentation</h1>', $responses['docs']->getContent());
        $this->assertStringContainsString('Mailing Lists', $responses['support']->getContent());
    }

    /**
     * @depends testSend
     */
    public function testFollowLocation()
    {
        $client = $this->createClient();
        $client->baseUrl = 'https://www.php.net/';

        $request = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('search.php')
            ->setData([
                'show' => 'quickref',
                'pattern' => 'array_merge'
            ]);

        $response = $request->setOptions([
            'followLocation' => false,
        ])->send();
        $this->assertEquals('302', $response->statusCode);

        $response = $request->setOptions([
            'followLocation' => true,
        ])->send();
        $this->assertResponseIsOK($response);
    }

    /**
     * @depends testSend
     */
    public function testSendError()
    {
        $client = $this->createClient();
        $client->baseUrl = 'http://unexisting.domain';
        $request = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('unexisting.php')
            ->addOptions(['timeout' => 1]);

        $this->expectException('yii\httpclient\Exception');

        $request->send();
    }

    /**
     * @depends testSend
     */
    public function testSendEvents()
    {
        $client = $this->createClient();
        $client->baseUrl = 'https://www.php.net/';

        $request = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('docs.php');

        $beforeSendEvent = null;
        $request->on(Request::EVENT_BEFORE_SEND, function(RequestEvent $event) use (&$beforeSendEvent) {
            $beforeSendEvent = $event;
        });

        $afterSendEvent = null;
        $request->on(Request::EVENT_AFTER_SEND, function(RequestEvent $event) use (&$afterSendEvent) {
            $afterSendEvent = $event;
        });

        $response = $request->send();

        $this->assertTrue($beforeSendEvent instanceof RequestEvent);
        $this->assertSame($request, $beforeSendEvent->request);
        $this->assertNull($beforeSendEvent->response);

        $this->assertTrue($afterSendEvent instanceof RequestEvent);
        $this->assertSame($request, $afterSendEvent->request);
        $this->assertSame($response, $afterSendEvent->response);
    }

    /**
     * @depends testSendEvents
     */
    public function testClientSendEvents()
    {
        $client = $this->createClient();
        $client->baseUrl = 'https://www.php.net/';

        $request = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('docs.php');

        $beforeSendEvent = null;
        $client->on(Client::EVENT_BEFORE_SEND, function(RequestEvent $event) use (&$beforeSendEvent) {
            $beforeSendEvent = $event;
        });

        $afterSendEvent = null;
        $client->on(Client::EVENT_AFTER_SEND, function(RequestEvent $event) use (&$afterSendEvent) {
            $afterSendEvent = $event;
        });

        $response = $request->send();

        $this->assertTrue($beforeSendEvent instanceof RequestEvent);
        $this->assertSame($request, $beforeSendEvent->request);
        $this->assertNull($beforeSendEvent->response);

        $this->assertTrue($afterSendEvent instanceof RequestEvent);
        $this->assertSame($request, $afterSendEvent->request);
        $this->assertSame($response, $afterSendEvent->response);
    }

    /**
     * @depends testBatchSend
     * @depends testClientSendEvents
     */
    public function testBatchSendEvents()
    {
        $client = $this->createClient();
        $client->baseUrl = 'https://www.php.net';

        $beforeSendUrls = [];
        $client->on(Client::EVENT_BEFORE_SEND, function(RequestEvent $event) use (&$beforeSendUrls) {
            $beforeSendUrls[] = $event->request->getFullUrl();
        });

        $afterSendUrls = [];
        $client->on(Client::EVENT_AFTER_SEND, function(RequestEvent $event) use (&$afterSendUrls) {
            $afterSendUrls[] = $event->request->getFullUrl();
        });

        $requests = [];
        $requests['docs'] = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('docs.php');
        $requests['support'] = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('support.php');

        $responses = $client->batchSend($requests);

        $expectedUrls = [
            $client->baseUrl . '/docs.php',
            $client->baseUrl . '/support.php',
        ];
        $this->assertEquals($expectedUrls, $beforeSendUrls);
        $this->assertEquals($expectedUrls, $afterSendUrls);
    }

    public function testInvalidUrl()
    {
        $client = $this->createClient();
        $request = $client->get('httpz:/example.com');
        $this->assertEquals('httpz:/example.com', $request->fullUrl);

        $this->expectException('yii\httpclient\Exception');
        $request->send();
    }

    /**
     * @depends testSend
     */
    public function testCustomSslCertificate()
    {
        if (!function_exists('openssl_pkey_new')) {
            $this->markTestSkipped('OpenSSL PHP extension required.');
        }

        $dn = [
            'countryName' => 'GB',
            'stateOrProvinceName' => 'State',
            'localityName' => 'SomewhereCity',
            'organizationName' => 'MySelf',
            'organizationalUnitName' => 'Whatever',
            'commonName' => 'mySelf',
            'emailAddress' => 'user@domain.com'
        ];
        $passphrase = '1234';

        $res = openssl_pkey_new();
        $csr = openssl_csr_new($dn, $res);
        $sscert = openssl_csr_sign($csr, null, $res, 365);
        openssl_x509_export($sscert, $publicKey);
        openssl_pkey_export($res, $privateKey, $passphrase);
        openssl_csr_export($csr, $csrStr);

        $filePath = Yii::getAlias('@runtime');
        FileHelper::createDirectory($filePath);

        $privateKeyFilename = $filePath . DIRECTORY_SEPARATOR . 'private.pem';
        $publicKeyFilename = $filePath . DIRECTORY_SEPARATOR . 'public.pem';

        file_put_contents($publicKeyFilename, $publicKey);
        file_put_contents($privateKeyFilename, $privateKey);

        $client = $this->createClient();
        $client->baseUrl = 'https://secure.php.net/';
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('docs.php')
            ->setOptions([
                'sslLocalCert' => $publicKeyFilename,
                'sslLocalPk' => $privateKeyFilename,
                'sslPassphrase' => $passphrase,
            ])
            ->send();

        $this->assertResponseIsOK($response);
        $content = $response->getContent();
        $this->assertNotEmpty($content);
        $this->assertStringContainsString('<h1>Documentation</h1>', $content);
    }
}
