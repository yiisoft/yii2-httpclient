<?php

namespace yiiunit\httpclient;

use yii\httpclient\Message;
use yii\http\Cookie;
use yii\http\CookieCollection;

class MessageTest extends TestCase
{
    public function testSetupHeaders()
    {
        $message = new Message();

        $headers = [
            'header1' => 'value1',
            'header2' => 'value2',
        ];
        $message->setHeaders($headers);

        $expectedHeaders = [
            'header1' => ['value1'],
            'header2' => ['value2'],
        ];
        $this->assertEquals($expectedHeaders, $message->getHeaders());

        $message->addHeader('header3', 'value3');

        $expectedHeaders = [
            'header1' => ['value1'],
            'header2' => ['value2'],
            'header3' => ['value3'],
        ];
        $this->assertEquals($expectedHeaders, $message->getHeaders());
    }

    /**
     * @depends testSetupHeaders
     */
    public function testSetupRawHeaders()
    {
        $message = new Message();

        $headers = [
            'header1: value1',
            'header2: value2',
        ];
        $message->setHeaders($headers);

        $expectedHeaders = [
            'header1' => ['value1'],
            'header2' => ['value2'],
        ];
        $this->assertEquals($expectedHeaders, $message->getHeaders());
    }

    /**
     * @depends testSetupRawHeaders
     */
    public function testParseHttpCode()
    {
        $message = new Message();

        $headers = [
            'HTTP/1.0 404 Not Found',
            'header1: value1',
        ];
        $message->setHeaders($headers);
        $this->assertEquals('404', $message->getHeaderLine('http-code'));

        $headers = [
            'HTTP/1.0 400 {some: "json"}',
            'header1: value1',
        ];
        $message->setHeaders($headers);
        $this->assertEquals('400', $message->getHeaderLine('http-code'));
    }

    /**
     * @depends testSetupHeaders
     */
    public function testHasHeader()
    {
        $message = new Message();

        $this->assertFalse($message->hasHeader('foo'));

        $message->addHeader('foo', 'some');
        $this->assertTrue($message->hasHeader('foo'));
    }

    public function testSetupCookies()
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
    public function testHasCookies()
    {
        $message = new Message();

        $this->assertFalse($message->hasCookies());

        $message->getCookies(); // instantiate `CookieCollection`
        $this->assertFalse($message->hasCookies());

        $message->getCookies()->add(new Cookie());
        $this->assertTrue($message->hasCookies());
    }

    public function testSetupFormat()
    {
        $message = new Message();

        $format = 'json';
        $message->setFormat($format);
        $this->assertEquals($format, $message->getFormat());
    }

    public function testSetupBody()
    {
        $message = new Message();
        $content = 'test raw body';
        $message->setContent($content);
        $this->assertEquals($content, $message->getContent());
    }

    public function testSetupData()
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
}