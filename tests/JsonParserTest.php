<?php

namespace yiiunit\extensions\httpclient;

use yii\helpers\Json;
use yii\httpclient\JsonParser;
use yii\httpclient\Response;

class JsonParserTest extends TestCase
{
    public function testParse()
    {
        $document = new Response();
        $data = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];
        $document->setContent(Json::encode($data));

        $parser = new JsonParser();
        $this->assertEquals($data, $parser->parse($document));
    }
}