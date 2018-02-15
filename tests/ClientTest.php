<?php

namespace yiiunit\httpclient;

use yii\http\MemoryStream;
use yii\httpclient\Client;
use yii\httpclient\JsonFormatter;
use yii\httpclient\UrlEncodedFormatter;
use yii\httpclient\XmlFormatter;
use yii\httpclient\JsonParser;
use yii\httpclient\UrlEncodedParser;
use yii\httpclient\XmlParser;
use yii\httpclient\Request;
use yii\httpclient\Response;
use yii\httpclient\Transport;
use yii\httpclient\CurlTransport;

class ClientTest extends TestCase
{
    public function testSetupFormatters()
    {
        $client = new Client();
        $client->formatters = [
            'testString' => UrlEncodedFormatter::class,
            'testConfig' => [
                'class' => UrlEncodedFormatter::class,
                'encodingType' => PHP_QUERY_RFC3986
            ],
        ];

        $formatter = $client->getFormatter('testString');
        $this->assertTrue($formatter instanceof UrlEncodedFormatter);

        $formatter = $client->getFormatter('testConfig');
        $this->assertTrue($formatter instanceof UrlEncodedFormatter);
        $this->assertEquals(PHP_QUERY_RFC3986, $formatter->encodingType);
    }

    /**
     * Data provider for [[testGetDefaultFormatters]]
     * @return array test data
     */
    public function dataProviderDefaultFormatters()
    {
        return [
            [Client::FORMAT_JSON, JsonFormatter::class],
            [Client::FORMAT_URLENCODED, UrlEncodedFormatter::class],
            [Client::FORMAT_RAW_URLENCODED, UrlEncodedFormatter::class],
            [Client::FORMAT_XML, XmlFormatter::class],
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
            Client::FORMAT_JSON => UrlEncodedFormatter::class,
        ];
        $formatter = $client->getFormatter(Client::FORMAT_JSON);
        $this->assertTrue($formatter instanceof UrlEncodedFormatter);
    }

    public function testSetupParsers()
    {
        $client = new Client();
        $client->parsers = [
            'testString' => UrlEncodedParser::class,
            'testConfig' => [
                'class' => UrlEncodedParser::class,
            ],
        ];

        $parser = $client->getParser('testString');
        $this->assertTrue($parser instanceof UrlEncodedParser);

        $parser = $client->getParser('testConfig');
        $this->assertTrue($parser instanceof UrlEncodedParser);
    }

    /**
     * Data provider for [[testGetDefaultParsers]]
     * @return array test data
     */
    public function dataProviderDefaultParsers()
    {
        return [
            [Client::FORMAT_JSON, JsonParser::class],
            [Client::FORMAT_URLENCODED, UrlEncodedParser::class],
            [Client::FORMAT_RAW_URLENCODED, UrlEncodedParser::class],
            [Client::FORMAT_XML, XmlParser::class],
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
            Client::FORMAT_JSON => UrlEncodedParser::class,
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

        $client->setTransport(CurlTransport::class);
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

        $body = new MemoryStream();
        $client->requestConfig = [
            'body' => $body
        ];
        $request = $client->createRequest();
        $this->assertSame($body, $request->getBody());
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
        $this->assertEquals($responseContent, $response->getBody()->__toString());
    }
} 