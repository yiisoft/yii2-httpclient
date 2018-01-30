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

    /**
     * @depends testParse
     */
    public function testParseEncoding()
    {
        $response = new Response();
        $xml = <<<XML
<?xml version="1.0" encoding="windows-1251"?>
<main>
    <enname>test</enname>
    <rusname>тест</rusname>
</main>
XML;
        $response->setContent($xml);
        $response->addHeaders(['content-type' => 'text/xml; charset=windows-1251']);

        $parser = new XmlParser();
        $data = $parser->parse($response);
        $this->assertEquals('test', $data['enname']);
        // UTF characters should be broken during parsing by 'windows-1251'
        $this->assertNotEquals('тест', $data['rusname']);
    }

    /**
     * @see https://github.com/yiisoft/yii2-httpclient/issues/102
     *
     * @depends testParse
     */
    public function testParseGroupTag()
    {
        $document = new Response();
        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<items>
    <item>
        <id>1</id>
        <name>item1</name>
    </item>
    <item>
        <id>2</id>
        <name>item2</name>
    </item>
</items>
XML;
        $document->setContent($xml);

        $data = [
            'item' => [
                [
                    'id' => '1',
                    'name' => 'item1',
                ],
                [
                    'id' => '2',
                    'name' => 'item2',
                ],
            ],
        ];
        $parser = new XmlParser();
        $this->assertEquals($data, $parser->parse($document));
    }
}
