<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\JsonFormatter;
use yii\httpclient\JsonParser;
use yii\httpclient\Request;
use yii\httpclient\Response;
use yii\httpclient\Transport;
use yii\httpclient\UrlEncodedFormatter;
use yii\httpclient\UrlEncodedParser;
use yii\httpclient\XmlFormatter;
use yii\httpclient\XmlParser;
use yii\web\HeaderCollection;

class ClientTest extends TestCase
{
    public function testSetupFormatters()
    {
        $client = new Client();
        $client->formatters = [
            'testString' => UrlEncodedFormatter::className(),
            'testConfig' => [
                'class' => UrlEncodedFormatter::className(),
                'encodingType' => PHP_QUERY_RFC3986
            ],
        ];

        $formatter = $client->getFormatter('testString');
        $this->assertTrue($formatter instanceof UrlEncodedFormatter);

        $formatter = $client->getFormatter('testConfig');
        $this->assertTrue($formatter instanceof UrlEncodedFormatter);
        $this->assertEquals(PHP_QUERY_RFC3986, $formatter->encodingType);
    }

    public function testGetUnrecognizedFormatter()
    {
        $client = new Client();
        $unrecognizedFormat = 'unrecognizedFormat';
        $this->expectException('\yii\base\InvalidParamException');
        $this->expectExceptionMessage("Unrecognized format '{$unrecognizedFormat}'");
        $client->getFormatter($unrecognizedFormat);
    }

    /**
     * Data provider for [[testGetDefaultFormatters]]
     * @return array test data
     */
    public function dataProviderDefaultFormatters()
    {
        return [
            [Client::FORMAT_JSON, JsonFormatter::className()],
            [Client::FORMAT_URLENCODED, UrlEncodedFormatter::className()],
            [Client::FORMAT_RAW_URLENCODED, UrlEncodedFormatter::className()],
            [Client::FORMAT_XML, XmlFormatter::className()],
        ];
    }

    /**
     * @dataProvider dataProviderDefaultFormatters
     *
     * @param string $format
     * @param string $expectedClass
     */
    public function testGetDefaultFormatters($format, $expectedClass)
    {
        $client = new Client();

        $formatter = $client->getFormatter($format);
        $this->assertTrue($formatter instanceof $expectedClass);
    }

    /**
     * @depends testSetupFormatters
     * @depends testGetDefaultFormatters
     */
    public function testOverrideDefaultFormatter()
    {
        $client = new Client();
        $client->formatters = [
            Client::FORMAT_JSON => UrlEncodedFormatter::className(),
        ];
        $formatter = $client->getFormatter(Client::FORMAT_JSON);
        $this->assertTrue($formatter instanceof UrlEncodedFormatter);
    }

    public function testSetupParsers()
    {
        $client = new Client();
        $client->parsers = [
            'testString' => UrlEncodedParser::className(),
            'testConfig' => [
                'class' => UrlEncodedParser::className(),
            ],
        ];

        $parser = $client->getParser('testString');
        $this->assertTrue($parser instanceof UrlEncodedParser);

        $parser = $client->getParser('testConfig');
        $this->assertTrue($parser instanceof UrlEncodedParser);
    }

    public function testGetUnrecognizedParser()
    {
        $client = new Client();
        $unrecognizedParser = 'unrecognizedParser';
        $this->expectException('\yii\base\InvalidParamException');
        $this->expectExceptionMessage("Unrecognized format '{$unrecognizedParser}'");
        $client->getParser($unrecognizedParser);
    }

    /**
     * Data provider for [[testGetDefaultParsers]]
     * @return array test data
     */
    public function dataProviderDefaultParsers()
    {
        return [
            [Client::FORMAT_JSON, JsonParser::className()],
            [Client::FORMAT_URLENCODED, UrlEncodedParser::className()],
            [Client::FORMAT_RAW_URLENCODED, UrlEncodedParser::className()],
            [Client::FORMAT_XML, XmlParser::className()],
        ];
    }

    /**
     * @dataProvider dataProviderDefaultParsers
     *
     * @param string $format
     * @param string $expectedClass
     */
    public function testGetDefaultParsers($format, $expectedClass)
    {
        $client = new Client();

        $parser = $client->getParser($format);
        $this->assertTrue($parser instanceof $expectedClass);
    }

    /**
     * @depends testSetupParsers
     * @depends testGetDefaultParsers
     */
    public function testOverrideDefaultParser()
    {
        $client = new Client();
        $client->parsers = [
            Client::FORMAT_JSON => UrlEncodedParser::className(),
        ];

        $parser = $client->getParser(Client::FORMAT_JSON);
        $this->assertTrue($parser instanceof UrlEncodedParser);
    }

    public function testSetupTransport()
    {
        $client = new Client();

        $transport = new CurlTransport();
        $client->setTransport($transport);
        $this->assertSame($transport, $client->getTransport());

        $client->setTransport(CurlTransport::className());
        $transport = $client->getTransport();
        $this->assertTrue($transport instanceof CurlTransport);
    }

    /**
     * @depends testSetupTransport
     */
    public function testGetDefaultTransport()
    {
        $client = new Client();
        $transport = $client->getTransport();
        $this->assertTrue($transport instanceof Transport);
    }

    public function testCreateRequest()
    {
        $client = new Client();

        $request = $client->createRequest();
        $this->assertTrue($request instanceof Request);
        $this->assertSame($client, $request->client);

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
        $this->assertSame($client, $response->client);

        $responseFormat = 'testFormat';
        $responseContent = 'test content';
        $client->responseConfig = [
            'format' => $responseFormat
        ];
        $response = $client->createResponse($responseContent);
        $this->assertEquals($responseFormat, $response->getFormat());
        $this->assertEquals($responseContent, $response->getContent());
    }

    public function testCreateResponseWithHeadersEqualToEmptyArray()
    {
        $client = new Client();
        $response = $client->createResponse('content', []);
        $headersCollection = $response->getHeaders();
        $this->assertInstanceOf(Response::className(), $response);
        $this->assertInstanceOf(HeaderCollection::className(), $headersCollection);
        $this->assertEquals([], $headersCollection->toArray());
    }

    public function testCreateRequestShortcut()
    {
        $method = 'POST';
        $url = 'url';
        $data = ['data'];
        $headers = ['headers'];
        $options = ['options'];

        $client = new Client();
        /** @var Request $request */
        $request = $this->invoke($client, 'createRequestShortcut', [$method, $url, $data, $headers, $options]);

        $this->assertEquals($method, $request->getMethod());
        $this->assertEquals($url, $request->getUrl());
        $this->assertEquals($data, $request->getData());
        $this->assertEquals($headers, $request->getHeaders()->toArray()[0]);
        $this->assertEquals($options, $request->getOptions());
    }

    public function testRequestShortcutMethods()
    {
        $url = 'url';
        $data = 'data';
        $headers = ['headers'];
        $options = ['options'];

        $client = $this->getMockBuilder('\yii\httpclient\Client')
            ->setMethods(['createRequestShortcut'])
            ->getMock();

        $client->expects($this->exactly(7))
            ->method('createRequestShortcut')
            ->withConsecutive(
                [$this->equalTo('GET'), $this->equalTo($url), $this->equalTo($data), $this->equalTo($headers), $this->equalTo($options)],
                [$this->equalTo('POST'), $this->equalTo($url), $this->equalTo($data), $this->equalTo($headers), $this->equalTo($options)],
                [$this->equalTo('PUT'), $this->equalTo($url), $this->equalTo($data), $this->equalTo($headers), $this->equalTo($options)],
                [$this->equalTo('PATCH'), $this->equalTo($url), $this->equalTo($data), $this->equalTo($headers), $this->equalTo($options)],
                [$this->equalTo('DELETE'), $this->equalTo($url), $this->equalTo($data), $this->equalTo($headers), $this->equalTo($options)],
                [$this->equalTo('HEAD'), $this->equalTo($url), $this->equalTo(null), $this->equalTo($headers), $this->equalTo($options)],
                [$this->equalTo('OPTIONS'), $this->equalTo($url), $this->equalTo(null), $this->equalTo([]), $this->equalTo($options)]
            );

        $client->get($url, $data, $headers, $options);
        $client->post($url, $data, $headers, $options);
        $client->put($url, $data, $headers, $options);
        $client->patch($url, $data, $headers, $options);
        $client->delete($url, $data, $headers, $options);
        $client->head($url, $headers, $options);
        $client->options($url, $options);
    }
}