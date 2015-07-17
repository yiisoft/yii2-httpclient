<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\Client;
use yii\httpclient\FormatterJson;
use yii\httpclient\FormatterUrlEncoded;
use yii\httpclient\FormatterXml;
use yii\httpclient\ParserJson;
use yii\httpclient\ParserUrlEncoded;
use yii\httpclient\ParserXml;
use yii\httpclient\Request;
use yii\httpclient\Response;
use yii\httpclient\Transport;
use yii\httpclient\TransportCurl;

class ClientTest extends TestCase
{
    public function testSetupFormatters()
    {
        $client = new Client();
        $client->formatters = [
            'testString' => FormatterUrlEncoded::className(),
            'testConfig' => [
                'class' => FormatterUrlEncoded::className(),
                'encodingType' => PHP_QUERY_RFC3986
            ],
        ];

        $formatter = $client->getFormatter('testString');
        $this->assertTrue($formatter instanceof FormatterUrlEncoded);

        $formatter = $client->getFormatter('testConfig');
        $this->assertTrue($formatter instanceof FormatterUrlEncoded);
        $this->assertEquals(PHP_QUERY_RFC3986, $formatter->encodingType);
    }

    /**
     * Data provider for [[testGetDefaultFormatters]]
     * @return array test data
     */
    public function dataProviderDefaultFormatters()
    {
        return [
            [Client::FORMAT_JSON, FormatterJson::className()],
            [Client::FORMAT_URLENCODED, FormatterUrlEncoded::className()],
            [Client::FORMAT_RAW_URLENCODED, FormatterUrlEncoded::className()],
            //[Client::FORMAT_XML, FormatterXml::className()],
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
            Client::FORMAT_JSON => FormatterUrlEncoded::className(),
        ];
        $formatter = $client->getFormatter(Client::FORMAT_JSON);
        $this->assertTrue($formatter instanceof FormatterUrlEncoded);
    }

    public function testSetupParsers()
    {
        $client = new Client();
        $client->parsers = [
            'testString' => ParserUrlEncoded::className(),
            'testConfig' => [
                'class' => ParserUrlEncoded::className(),
            ],
        ];

        $parser = $client->getParser('testString');
        $this->assertTrue($parser instanceof ParserUrlEncoded);

        $parser = $client->getParser('testConfig');
        $this->assertTrue($parser instanceof ParserUrlEncoded);
    }

    /**
     * Data provider for [[testGetDefaultParsers]]
     * @return array test data
     */
    public function dataProviderDefaultParsers()
    {
        return [
            [Client::FORMAT_JSON, ParserJson::className()],
            [Client::FORMAT_URLENCODED, ParserUrlEncoded::className()],
            [Client::FORMAT_RAW_URLENCODED, ParserUrlEncoded::className()],
            [Client::FORMAT_XML, ParserXml::className()],
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
            Client::FORMAT_JSON => ParserUrlEncoded::className(),
        ];

        $parser = $client->getParser(Client::FORMAT_JSON);
        $this->assertTrue($parser instanceof ParserUrlEncoded);
    }

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
} 