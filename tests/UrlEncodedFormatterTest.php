<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\Request;
use yii\httpclient\UrlEncodedFormatter;

class UrlEncodedFormatterTest extends TestCase
{
    protected function setUp(): void
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
        $request->setUrl('https://yiiframework.com/');

        $formatter = new UrlEncodedFormatter();
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

    public function testFormatPutRequestWithInfileOption()
    {
        $fh = fopen(__DIR__ . '/test_file.txt', 'r');

        $request = new Request();
        $request->setMethod('PUT');
        $request->setOptions([
            CURLOPT_INFILE => $fh,
            CURLOPT_INFILESIZE => filesize(__DIR__ . '/test_file.txt'),
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_PUT => 1,
        ]);

        $formatter = new UrlEncodedFormatter();
        $formatter->format($request);

        $headers = $request->getHeaders()->toArray();

        $expectedHeaders = [
            'content-type' =>
                [
                    0 => 'application/x-www-form-urlencoded; charset=UTF-8',
                ],
        ];

        $this->assertEquals($expectedHeaders, $headers);
    }
}
