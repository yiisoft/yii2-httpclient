<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\Client;
use yii\httpclient\Request;
use yii\httpclient\Response;
use yii\httpclient\Transport;
use yii\httpclient\TransportCurl;

class ClientTest extends TestCase
{
    public function testSetupTransport()
    {
        $client = new Client();

        $transport = new TransportCurl();
        $client->setTransport($transport);
        $this->assertSame($transport, $client->getTransport());
        $this->assertSame($client, $transport->client);

        $client->setTransport(TransportCurl::className());
        $transport = $client->getTransport();
        $this->assertTrue($transport instanceof TransportCurl);
        $this->assertSame($client, $transport->client);
    }

    /**
     * @depends testSetupTransport
     */
    public function testGetDefaultTransport()
    {
        $client = new Client();
        $transport = $client->getTransport();
        $this->assertTrue($transport instanceof Transport);
        $this->assertSame($client, $transport->client);
    }

    public function testCreateRequest()
    {
        $client = new Client();

        $request = $client->createRequest();
        $this->assertTrue($request instanceof Request);
        $this->assertEquals($client, $request->client);

        $requestContent = 'test content';
        $client->requestConfig = [
            'content' => $requestContent
        ];
        $request = $client->createRequest();
        $this->assertEquals($requestContent, $request->getContent());
    }

    public function testCreateResponse()
    {
        $client = new Client();

        $response = $client->createResponse();
        $this->assertTrue($response instanceof Response);

        $responseFormat = 'testFormat';
        $responseContent = 'test content';
        $client->responseConfig = [
            'format' => $responseFormat
        ];
        $response = $client->createResponse($responseContent);
        $this->assertEquals($responseFormat, $response->getFormat());
        $this->assertEquals($responseContent, $response->getContent());
    }


} 