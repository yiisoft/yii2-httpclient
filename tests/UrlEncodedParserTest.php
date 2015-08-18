<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\UrlEncodedParser;
use yii\httpclient\Response;

class UrlEncodedParserTest extends TestCase
{
    public function testParse()
    {
        $document = new Response();
        $data = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];
        $document->setContent(http_build_query($data));

        $parser = new UrlEncodedParser();
        $this->assertEquals($data, $parser->parse($document));
    }
} 