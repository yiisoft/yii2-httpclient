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
    }
}