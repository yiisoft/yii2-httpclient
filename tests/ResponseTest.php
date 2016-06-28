<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\Client;
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
                Client::FORMAT_URLENCODED
            ],
            [
                'application/json',
                Client::FORMAT_JSON
            ],
            [
                'text/xml',
                Client::FORMAT_XML
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
                Client::FORMAT_URLENCODED
            ],
            [
                '{"name1":"value1", "name2":"value2"}',
                Client::FORMAT_JSON
            ],
            [
                '<?xml version="1.0" encoding="utf-8"?><root></root>',
                Client::FORMAT_XML
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

    public function testParseBody()
    {
        $response = new Response([
            'client' => new Client(),
            'format' => Client::FORMAT_URLENCODED,
        ]);

        $content = 'name=value';
        $response->setContent($content);
        $this->assertEquals(['name' => 'value'], $response->getData());
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
        $this->assertTrue($cookie instanceof Cookie);

        $response = new Response();
        $response->setHeaders(['set-cookie' => [
            'name1=value1; path=/; httponly',
            'name2=value2; path=/; httponly',
        ]]);
        $this->assertEquals(2, $response->getCookies()->count());

        // @see https://github.com/yiisoft/yii2-httpclient/issues/29
        $response = new Response();
        $response->setHeaders(['set-cookie' => 'extraParam=maxAge; path=/; httponly; Max-Age=3600']);
        $cookie = $response->getCookies()->get('extraParam');
        $this->assertTrue($cookie instanceof Cookie);
    }

    public function testToString()
    {
        $response = new Response([
            'headers' => [
                'content-type' => 'text/html; charset=UTF-8'
            ],
            'content' => '<html>Content</html>'
        ]);

        $expectedResult = <<<EOL
Content-Type: text/html; charset=UTF-8

<html>Content</html>
EOL;
        $this->assertEquals($expectedResult, $response->toString());
    }
}