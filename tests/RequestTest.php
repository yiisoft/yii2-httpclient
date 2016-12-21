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
     * Data provider for [[testGetFullUrl()]]
     * @return array test data
     */
    public function dataProviderGetFullUrl()
    {
        return [
            [
                'http://some-domain.com',
                'test/url',
                'http://some-domain.com/test/url'
            ],
            [
                'http://some-domain.com',
                'http://another-domain.com/test',
                'http://another-domain.com/test',
            ],
            [
                'http://some-domain.com',
                ['test/url', 'param1' => 'name1'],
                'http://some-domain.com/test/url?param1=name1'
            ],
            [
                'http://some-domain.com?base-param=base',
                null,
                'http://some-domain.com?base-param=base',
            ],
            [
                'http://some-domain.com?base-param=base',
                ['param1' => 'name1'],
                'http://some-domain.com?base-param=base&param1=name1',
            ],
        ];
    }

    /**
     * @depends testSetupUrl
     * @dataProvider dataProviderGetFullUrl
     *
     * @param string $baseUrl
     * @param mixed $url
     * @param string $expectedFullUrl
     */
    public function testGetFullUrl($baseUrl, $url, $expectedFullUrl)
    {
        $client = new Client();
        $client->baseUrl = $baseUrl;
        $request = new Request(['client' => $client]);

        $request->setUrl($url);
        $this->assertEquals($expectedFullUrl, $request->getFullUrl());
    }

    /**
     * @depends testToString
     */
    public function testReuse()
    {
        $request = new Request([
            'client' => new Client(),
            'format' => Client::FORMAT_URLENCODED,
            'method' => 'post',
            'url' => 'http://domain.com/test',
        ]);

        $data = [
            'param1' => 'value1',
        ];
        $request->setData($data);

        $expectedResult = <<<EOL
POST http://domain.com/test
Content-Type: application/x-www-form-urlencoded; charset=UTF-8

param1=value1
EOL;
        $this->assertEquals($expectedResult, $request->toString());

        $data = [
            'param2' => 'value2',
        ];
        $request->setData($data);

        $expectedResult = <<<EOL
POST http://domain.com/test
Content-Type: application/x-www-form-urlencoded; charset=UTF-8

param2=value2
EOL;
        $this->assertEquals($expectedResult, $request->toString());
    }
} 