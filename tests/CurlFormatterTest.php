<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\CurlFormatter;
use yii\httpclient\Request;

class CurlFormatterTest extends TestCase
{
    protected function setUp()
    {
        $this->mockApplication();
    }

    // Tests :

    public function testFormat()
    {
        if (class_exists('\CURLFile')) {
            $data = [
                'name1' => 'value1',
                'name2' => 'value2',
                'file1' => new \CURLFile('/path/to/file1'),
                'file2' => new \CURLFile('/path/to/file2'),
            ];
        } else {
            $data = [
                'name1' => 'value1',
                'name2' => 'value2',
            ];
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
        $request->setMethod('GET');
        $data = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];
        $request->setData($data);

        $formatter = new CurlFormatter();
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