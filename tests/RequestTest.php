<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\Client;
use yii\httpclient\Request;

class RequestTest extends TestCase
{
    protected function setUp()
    {
        $this->mockApplication();
    }

    // Tests :

    public function testSetupUrl()
    {
        $request = new Request();

        $url = 'test/url';
        $request->setUrl($url);
        $this->assertEquals($url, $request->getUrl());
    }

    public function testSetupMethod()
    {
        $request = new Request();

        $method = 'put';
        $request->setMethod($method);
        $this->assertEquals($method, $request->getMethod());
    }

    public function testSetupOptions()
    {
        $request = new Request();

        $options = [
            'timeout' => 10,
            'userAgent' => 'test',
        ];
        $request->setOptions($options);
        $this->assertEquals($options, $request->getOptions());
    }

    /**
     * @depends testSetupOptions
     */
    public function testAddOptions()
    {
        $request = new Request();

        $options = [
            'timeout' => 10,
            'userAgent' => 'test',
        ];
        $request->setOptions($options);

        $request->addOptions([
            'userAgent' => 'override',
            'test' => 'new',
        ]);

        $expectedOptions = [
            'timeout' => 10,
            'userAgent' => 'override',
            'test' => 'new',
        ];
        $this->assertEquals($expectedOptions, $request->getOptions());
    }

    /**
     * @depends testSetupMethod
     */
    public function testFormatData()
    {
        $request = new Request([
            'client' => new Client(),
            'format' => Client::FORMAT_URLENCODED,
            'method' => 'post',
        ]);

        $data = [
            'name' => 'value',
        ];
        $request->setData($data);
        $request->prepare();
        $this->assertEquals('name=value', $request->getContent());
    }

    /**
     * @depends testFormatData
     */
    public function testToString()
    {
        $request = new Request([
            'client' => new Client(),
            'format' => Client::FORMAT_URLENCODED,
            'method' => 'post',
            'url' => 'http://domain.com/test',
        ]);

        $data = [
            'name' => 'value',
        ];
        $request->setData($data);

        $expectedResult = <<<EOL
POST http://domain.com/test
Content-Type: application/x-www-form-urlencoded; charset=UTF-8

name=value
EOL;
        $this->assertEquals($expectedResult, $request->toString());

        // @see https://github.com/yiisoft/yii2-httpclient/issues/70
        $request = new Request([
            'client' => new Client(),
            'format' => Client::FORMAT_URLENCODED,
            'method' => 'post',
            'url' => 'http://domain.com/test',
        ]);
        $request->setData($data);
        $request->addFileContent('some-file', 'some content');

        $result = $request->toString();
        $this->assertContains('Content-Type: multipart/form-data; boundary=', $result);
        $this->assertContains('some content', $result);
    }

    /**
     * @depends testSetupUrl
     */
    public function testGetFullUrl()
    {
        $client = new Client();
        $client->baseUrl = 'http://some-domain.com';
        $request = new Request(['client' => $client]);

        $url = 'test/url';
        $request->setUrl($url);
        $this->assertEquals('http://some-domain.com/test/url', $request->getFullUrl());

        $url = 'http://another-domain.com/test';
        $request->setUrl($url);
        $this->assertEquals($url, $request->getFullUrl());

        $url = ['test/url', 'param1' => 'name1'];
        $request->setUrl($url);
        $this->assertEquals('http://some-domain.com/test/url?param1=name1', $request->getFullUrl());
    }
} 