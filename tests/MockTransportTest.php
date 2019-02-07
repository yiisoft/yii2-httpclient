<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\MockTransport;
use yii\httpclient\Request;
use yii\httpclient\Response;

final class MockTransportTest extends TestCase
{
    /**
     * @var MockTransport
     */
    private $transport;

    protected function setUp()
    {
        $this->transport = new MockTransport();
    }

    public function testResponseIsGivenByTheUser()
    {
        $request = new Request();
        $response = new Response();

        $this->transport->appendResponse($response);

        $this->assertSame($response, $this->transport->send($request));
        $this->assertSame([$request], $this->transport->flushRequests());
    }

    public function testCallingSendWithoutSettingTheResponseRaiseException()
    {
        $this->expectException('yii\httpclient\Exception');

        $this->transport->send(new Request());
    }

    public function testBatchResponsesAreFlushedInGivenOrder()
    {
        $requests = [
            new Request(),
            new Request(),
        ];
        $responses = [
            new Response(),
            new Response(),
        ];

        foreach ($responses as $response) {
            $this->transport->appendResponse($response);
        }

        $this->assertSame($responses, $this->transport->batchSend($requests));
        $this->assertSame($requests, $this->transport->flushRequests());
    }
}
