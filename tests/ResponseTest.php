<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\MessageInterface;
use yii\httpclient\Response;
use yii\web\Cookie;

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
            [201, true],
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

    public function testParseCookieHeader()
    {
        $response = new Response();
        $this->assertEquals(0, $response->getCookies()->count());

        $response = new Response();
        $response->setHeaders(['set-cookie' => 'name1=value1; path=/; httponly']);
        $this->assertEquals(1, $response->getCookies()->count());
        $cookie = $response->getCookies()->get('name1');
        $this->assertTrue($cookie instanceof Cookie);
        $this->assertEquals('value1', $cookie->value);
        $this->assertEquals('/', $cookie->path);
        $this->assertEquals(true, $cookie->httpOnly);

        $response = new Response();
        $response->setHeaders(['set-cookie' => 'COUNTRY=NA%2C195.177.208.1; expires=Thu, 23-Jul-2015 13:39:41 GMT; path=/; domain=.php.net']);
        $cookie = $response->getCookies()->get('COUNTRY');

        $response = new Response();
        $response->setHeaders(['set-cookie' => [
            'name1=value1; path=/; httponly',
            'name2=value2; path=/; httponly',
        ]]);
        $this->assertEquals(2, $response->getCookies()->count());
    }
}