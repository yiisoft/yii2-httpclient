<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\Message;
use yii\web\Cookie;
use yii\web\CookieCollection;
use yii\web\HeaderCollection;

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
    public function testSetupRawHeaders()
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
    public function testParseHttpCode()
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
    public function testHasHeaders()
    {
        $message = new Message();

        $this->assertFalse($message->hasHeaders());

        $message->getHeaders(); // instantiate `HeaderCollection`
        $this->assertFalse($message->hasHeaders());

        $message->getHeaders()->add('name', 'value');
        $this->assertTrue($message->hasHeaders());
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

    public function testUnableToMergeData()
    {
        $message = new Message();
        $this->expectException('\yii\base\Exception');
        $this->expectExceptionMessage('Unable to merge existing data with new data. Existing data is not an array.');
        $message->setData('not an array');
        $message->addData(['array']);
    }

    public function testToStringMagicMethod()
    {
        $message = new Message();
        $message->setContent('content');
        $string = $message . '';
        $this->assertEqualsWithoutLE('content', $string);
    }
}