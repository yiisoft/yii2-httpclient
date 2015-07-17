<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\FormatterUrlEncoded;
use yii\httpclient\Request;

class FormatterUrlEncodedTest extends TestCase
{
    public function testFormat()
    {
        $request = new Request();
        $request->setMethod('post');
        $data = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];
        $request->setData($data);

        $formatter = new FormatterUrlEncoded();
        $formatter->format($request);
        $this->assertEquals(http_build_query($data), $request->getContent());
        $this->assertEquals('application/x-www-form-urlencoded', $request->getHeaders()->get('Content-Type'));
    }

    public function testFormatMethodGet()
    {
        $request = new Request();
        $request->setMethod('get');
        $data = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];
        $request->setData($data);

        $formatter = new FormatterUrlEncoded();
        $formatter->format($request);
        $this->assertEmpty($request->getContent());
        $this->assertContains(http_build_query($data), $request->getUrl());
        $this->assertFalse($request->getHeaders()->has('Content-Type'));
    }
} 