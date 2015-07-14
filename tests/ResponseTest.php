<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\MessageInterface;
use yii\httpclient\Response;

class ResponseTest extends TestCase
{
    /**
     * Data provider for [[testDetectFormatByHeaders()]]
     * @return array test data
     */
    public function dataProviderDetectFormatByHeaders()
    {
        return [
            [
                'application/x-www-form-urlencoded',
                MessageInterface::FORMAT_URLENCODED
            ],
            [
                'application/json',
                MessageInterface::FORMAT_JSON
            ],
            [
                'text/xml',
                MessageInterface::FORMAT_XML
            ],
        ];
    }

    /**
     * @dataProvider dataProviderDetectFormatByHeaders
     *
     * @param string $contentType
     * @param string $expectedFormat
     */
    public function testDetectFormatByHeaders($contentType, $expectedFormat)
    {
        $response = new Response();
        $response->setHeaders(['Content-type' => $contentType]);
        $this->assertEquals($expectedFormat, $response->getFormat());
    }

    /**
     * Data provider for [[testDetectFormatByContent()]]
     * @return array test data
     */
    public function dataProviderDetectFormatByContent()
    {
        return [
            [
                'name1=value1&name2=value2',
                MessageInterface::FORMAT_URLENCODED
            ],
            [
                '{"name1":"value1", "name2":"value2"}',
                MessageInterface::FORMAT_JSON
            ],
            [
                '<?xml version="1.0" encoding="utf-8"?><root></root>',
                MessageInterface::FORMAT_XML
            ],
        ];
    }

    /**
     * @dataProvider dataProviderDetectFormatByContent
     *
     * @param string $content
     * @param string $expectedFormat
     */
    public function testDetectFormatByContent($content, $expectedFormat)
    {
        $response = new Response();
        $response->setContent($content);
        $this->assertEquals($expectedFormat, $response->getFormat());
    }

    public function testGetStatusCode()
    {
        $response = new Response();
        $statusCode = 123;
        $response->setHeaders(['http-code' => $statusCode]);
        $this->assertEquals($statusCode, $response->getStatusCode());
    }

    /**
     * Data provider for [[testIsOk()]]
     * @return array test data.
     */
    public function dataProviderIsOk()
    {
        return [
            [200, true],
            [400, false],
        ];
    }

    /**
     * @dataProvider dataProviderIsOk
     * @depends testGetStatusCode
     *
     * @param integer $statusCode
     * @param boolean $isOk
     */
    public function testIsOk($statusCode, $isOk)
    {
        $response = new Response();
        $response->setHeaders(['http-code' => $statusCode]);
        $this->assertEquals($isOk, $response->getIsOk());
    }
}