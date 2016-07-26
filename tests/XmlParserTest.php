<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\XmlParser;
use yii\httpclient\Response;

class XmlParserTest extends TestCase
{
    public function testParse()
    {
        $document = new Response();
        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<main>
    <name1>value1</name1>
    <name2>value2</name2>
</main>
XML;
        $document->setContent($xml);

        $data = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];
        $parser = new XmlParser();
        $this->assertEquals($data, $parser->parse($document));
    }

    /**
     * @depends testParse
     */
    public function testParseCData()
    {
        $document = new Response();
        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<main>
    <name1><![CDATA[<tag>]]></name1>
    <name2><![CDATA[value2]]></name2>
</main>
XML;
        $document->setContent($xml);

        $data = [
            'name1' => '<tag>',
            'name2' => 'value2',
        ];
        $parser = new XmlParser();
        $this->assertEquals($data, $parser->parse($document));
    }
}