<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\Request;
use yii\httpclient\UrlEncodedFormatter;

class UrlEncodedFormatterTest extends TestCase
{
    protected function setUp()
    {
        $this->mockApplication();
    }

    // Tests :

    public function testFormat()
    {
        $request = new Request();
        $request->setMethod('POST');
        $data = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];
        $request->setData($data);

        $formatter = new UrlEncodedFormatter();
        $formatter->format($request);
        $this->assertEquals(http_build_query($data), $request->getContent());
        $this->assertEquals('application/x-www-form-urlencoded; charset=UTF-8', $request->getHeaders()->get('Content-Type'));
    }

    /**
     * @depends testFormat
     */
    public function testFormatMethodGet()
    {
        $request = new Request();
        $request->setMethod('GET');
        $data = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];
        $request->setData($data);

        $formatter = new UrlEncodedFormatter();
        $formatter->format($request);
        $this->assertEmpty($request->getContent());
        $this->assertContains(http_build_query($data), $request->getFullUrl());
        $this->assertFalse($request->getHeaders()->has('Content-Type'));
    }

    /**
     * @depends testFormatMethodGet
     */
    public function testFormatEmpty()
    {
        $request = new Request();
        $request->setMethod('head');

        $formatter = new UrlEncodedFormatter();
        $formatter->format($request);
        $this->assertNull($request->getContent());
    }

    public function testFormatPostRequestWithEmptyBody()
    {
        $request = new Request();
        $request->setMethod('POST');

        $formatter = new UrlEncodedFormatter();
        $formatter->format($request);

        $headers = $request->getHeaders();

        $this->assertEquals('0', $headers['content-length'][0]);
    }
}