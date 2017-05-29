<?php

namespace yiiunit\extensions\httpclient;

use yii\helpers\Json;
use yii\httpclient\JsonFormatter;
use yii\httpclient\Request;

class JsonFormatterTest extends TestCase
{
    public function testFormat()
    {
        $request = new Request();
        $data = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];
        $request->setData($data);

        $formatter = new JsonFormatter();
        $formatter->format($request);
        $this->assertEquals(Json::encode($data), $request->getContent());
        $this->assertEquals('application/json; charset=UTF-8', $request->getHeaders()->get('Content-Type'));
    }

    /**
     * @depends testFormat
     */
    public function testFormatEmpty()
    {
        $request = new Request();

        $formatter = new JsonFormatter();
        $formatter->format($request);
        $this->assertNull($request->getContent());
    }
} 