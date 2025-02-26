<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\CurlFormatter;
use yii\httpclient\Request;

class CurlFormatterTest extends TestCase
{
    protected function setUp(): void
    {
        $this->mockApplication();
    }

    // Tests :

    public function testFormat()
    {
        $data = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];
        if (class_exists('\CURLFile')) {
            $data['file1'] = new \CURLFile('/path/to/file1');
            $data['file2'] = new \CURLFile('/path/to/file2');
        }

        $request = new Request();
        $request->setMethod('POST');
        $request->setData($data);

        $formatter = new CurlFormatter();
        $formatter->format($request);
        $this->assertEquals($data, $request->getContent());
        $this->assertEquals(null, $request->getHeaders()->get('Content-Type'));
    }

    /**
     * @depends testFormat
     */
    public function testFormatMethodGet()
    {
        $request = new Request();
        $request->setUrl('https://yiiframework.com/');
        $request->setMethod('GET');
        $data = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];
        $request->setData($data);

        $formatter = new CurlFormatter();
        $formatter->format($request);
        $this->assertEmpty($request->getContent());
        $this->assertStringContainsString(http_build_query($data), $request->getFullUrl());
        $this->assertFalse($request->getHeaders()->has('Content-Type'));
    }

    /**
     * @depends testFormatMethodGet
     */
    public function testFormatEmpty()
    {
        $request = new Request();
        $request->setMethod('head');

        $formatter = new CurlFormatter();
        $formatter->format($request);
        $this->assertNull($request->getContent());
    }

    public function testFormatPostRequestWithEmptyBody()
    {
        $request = new Request();
        $request->setMethod('POST');

        $formatter = new CurlFormatter();
        $formatter->format($request);

        $headers = $request->getHeaders();

        $this->assertEquals('0', $headers['content-length'][0]);
    }
}
