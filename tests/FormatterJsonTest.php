<?php

namespace yiiunit\extensions\httpclient;

use yii\helpers\Json;
use yii\httpclient\FormatterJson;
use yii\httpclient\Request;

class FormatterJsonTest extends TestCase
{
    public function testFormat()
    {
        $request = new Request();
        $data = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];
        $request->setData($data);

        $formatter = new FormatterJson();
        $formatter->format($request);
        $this->assertEquals(Json::encode($data), $request->getContent());
        $this->assertEquals('application/json; charset=UTF-8', $request->getHeaders()->get('Content-Type'));
    }
} 