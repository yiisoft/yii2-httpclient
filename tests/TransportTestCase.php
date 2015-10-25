<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\Client;
use yii\httpclient\Response;

/**
 * This is the base class for HTTP message transport unit tests.
 */
abstract class TransportTestCase extends TestCase
{
    protected function setUp()
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

    public function testSend()
    {
        $client = $this->createClient();
        $client->baseUrl = 'http://us.php.net';
        $response = $client->createRequest()
            ->setMethod('get')
            ->setUrl('docs.php')
            ->send();

        $this->assertTrue($response->getIsOk());
        $content = $response->getContent();
        $this->assertNotEmpty($content);
        $this->assertContains('<h1>Documentation</h1>', $content);
    }

    /**
     * @depends testSend
     */
    public function testSendPost()
    {
        $client = $this->createClient();
        $client->baseUrl = 'http://us.php.net';
        $response = $client->createRequest()
            ->setMethod('post')
            ->setUrl('search.php')
            ->setData(['pattern' => 'curl'])
            ->send();
        $this->assertTrue($response->getIsOk());
    }

    /**
     * @depends testSend
     */
    public function testBatchSend()
    {
        $client = $this->createClient();
        $client->baseUrl = 'http://us.php.net';

        $requests = [];
        $requests['docs'] = $client->createRequest()
            ->setMethod('get')
            ->setUrl('docs.php');
        $requests['support'] = $client->createRequest()
            ->setMethod('get')
            ->setUrl('support.php');

        $responses = $client->batchSend($requests);
        $this->assertCount(count($requests), $responses);

        foreach ($responses as $name => $response) {
            $this->assertTrue($response->getIsOk());
        }

        $this->assertTrue($responses['docs'] instanceof Response, $responses);
        $this->assertTrue($responses['support'] instanceof Response, $responses);

        $this->assertContains('<h1>Documentation</h1>', $responses['docs']->getContent());
        $this->assertContains('Mailing Lists', $responses['support']->getContent());
    }

    /**
     * @depends testSend
     */
    public function testFollowLocation()
    {
        $client = $this->createClient();
        $client->baseUrl = 'http://us.php.net';

        $request = $client->createRequest()
            ->setMethod('get')
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
        $this->assertTrue($response->getIsOk());
    }
}