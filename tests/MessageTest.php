<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\Message;
use yii\web\Cookie;
use yii\web\CookieCollection;
use yii\web\HeaderCollection;

class MessageTest extends TestCase
{
    public function testSetupHeaders(): void
    {
        $message = new Message();

        $headers = [
            'header1' => 'value1',
            'header2' => 'value2',
        ];
        $message->setHeaders($headers);

        $this->assertTrue($message->getHeaders() instanceof HeaderCollection);
        $expectedHeaders = [
            'header1' => ['value1'],
            'header2' => ['value2'],
        ];
        $this->assertEquals($expectedHeaders, $message->getHeaders()->toArray());

        $additionalHeaders = [
            'header3' => 'value3'
        ];
        $message->addHeaders($additionalHeaders);

        $expectedHeaders = [
            'header1' => ['value1'],
            'header2' => ['value2'],
            'header3' => ['value3'],
        ];
        $this->assertEquals($expectedHeaders, $message->getHeaders()->toArray());
    }

    /**
     * @depends testSetupHeaders
     */
    public function testSetupRawHeaders(): void
    {
        $message = new Message();

        $headers = [
            'header1: value1',
            'header2: value2',
        ];
        $message->setHeaders($headers);

        $this->assertTrue($message->getHeaders() instanceof HeaderCollection);
        $expectedHeaders = [
            'header1' => ['value1'],
            'header2' => ['value2'],
        ];
        $this->assertEquals($expectedHeaders, $message->getHeaders()->toArray());
    }

    /**
     * @depends testSetupRawHeaders
     */
    public function testParseHttpCode(): void
    {
        $message = new Message();

        $headers = [
            'HTTP/1.0 404 Not Found',
            'header1: value1',
        ];
        $message->setHeaders($headers);
        $this->assertEquals('404', $message->getHeaders()->get('http-code'));

        $headers = [
            'HTTP/1.0 400 {some: "json"}',
            'header1: value1',
        ];
        $message->setHeaders($headers);
        $this->assertEquals('400', $message->getHeaders()->get('http-code'));
    }

    /**
     * @depends testSetupHeaders
     */
    public function testHasHeaders(): void
    {
        $message = new Message();

        $this->assertFalse($message->hasHeaders());

        $message->getHeaders(); // instantiate `HeaderCollection`
        $this->assertFalse($message->hasHeaders());

        $message->getHeaders()->add('name', 'value');
        $this->assertTrue($message->hasHeaders());
    }

    public function testSetupCookies(): void
    {
        $message = new Message();

        $cookies = [
            [
                'name' => 'test',
                'domain' => 'test.com',
            ],
        ];
        $message->setCookies($cookies);
        $cookieCollection = $message->getCookies();
        $this->assertTrue($cookieCollection instanceof CookieCollection);
        $cookie = $cookieCollection->get('test');
        $this->assertTrue($cookie instanceof Cookie);
        $this->assertEquals('test.com', $cookie->domain);

        $additionalCookies = [
            [
                'name' => 'additional',
                'domain' => 'additional.com',
            ],
        ];
        $message->addCookies($additionalCookies);
        $cookie = $cookieCollection->get('additional');
        $this->assertTrue($cookie instanceof Cookie);
        $this->assertEquals('additional.com', $cookie->domain);
    }

    /**
     * @depends testSetupCookies
     */
    public function testHasCookies(): void
    {
        $message = new Message();

        $this->assertFalse($message->hasCookies());

        $message->getCookies(); // instantiate `CookieCollection`
        $this->assertFalse($message->hasCookies());

        // cookie name must be a string
        $message->getCookies()->add(new Cookie(['name' => 'cookie-test']));
        $this->assertTrue($message->hasCookies());
    }

    public function testSetupFormat(): void
    {
        $message = new Message();

        $format = 'json';
        $message->setFormat($format);
        $this->assertEquals($format, $message->getFormat());
    }

    public function testSetupBody(): void
    {
        $message = new Message();
        $content = 'test raw body';
        $message->setContent($content);
        $this->assertEquals($content, $message->getContent());
    }

    public function testSetupData(): void
    {
        $message = new Message();
        $data = [
            'field1' => 'value1',
            'field2' => 'value2',
        ];
        $message->setData($data);
        $this->assertEquals($data, $message->getData());

        $additionalData = [
            'field3' => 'value3'
        ];
        $message->addData($additionalData);
        $this->assertEquals(array_merge($data, $additionalData), $message->getData());
    }

    public function testUnableToMergeData(): void
    {
        $message = new Message();
        $this->expectException('\yii\base\Exception');
        $this->expectExceptionMessage('Unable to merge existing data with new data. Existing data is not an array.');
        $message->setData('not an array');
        $message->addData(['array']);
    }

    public function testToStringMagicMethod(): void
    {
        $message = new Message();
        $message->setContent('content');
        $string = $message . '';
        $this->assertEqualsWithoutLE('content', $string);
    }

    public function testParseHttpStatusLineDetails(): void
    {
        $message = new Message();

        $message->setHeaders(
            [
                'HTTP/1.1 404 RequestNotFound',
                'header1: value1',
            ],
        );

        $headers = $message->getHeaders();

        $this->assertSame('404', $headers->get('http-code'));
        $this->assertSame('HTTP/1.1 404 RequestNotFound', $headers->get('http-status-line'));
        $this->assertSame('HTTP/1.1', $headers->get('http-version'));
        $this->assertSame('404 RequestNotFound', $headers->get('http-reason-phrase'));
    }
}
