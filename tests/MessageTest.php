<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\Message;
use yii\httpclient\FormatterUrlEncoded;
use yii\httpclient\ParserUrlEncoded;
use yii\web\HeaderCollection;

class MessageTest extends TestCase
{
    public function testSetupHeaders()
    {
        $document = new Message();

        $headers = [
            'header1' => 'value1',
            'header2' => 'value2',
        ];
        $document->setHeaders($headers);

        $this->assertTrue($document->getHeaders() instanceof HeaderCollection);
        $expectedHeaders = [
            'header1' => ['value1'],
            'header2' => ['value2'],
        ];
        $this->assertEquals($expectedHeaders, $document->getHeaders()->toArray());

        $additionalHeaders = [
            'header3' => 'value3'
        ];
        $document->addHeaders($additionalHeaders);

        $expectedHeaders = [
            'header1' => ['value1'],
            'header2' => ['value2'],
            'header3' => ['value3'],
        ];
        $this->assertEquals($expectedHeaders, $document->getHeaders()->toArray());
    }

    public function testSetupFormat()
    {
        $document = new Message();

        $format = 'json';
        $document->setFormat($format);
        $this->assertEquals($format, $document->getFormat());
    }

    public function testSetupBody()
    {
        $document = new Message();
        $content = 'test raw body';
        $document->setContent($content);
        $this->assertEquals($content, $document->getContent());
    }

    public function testSetupData()
    {
        $document = new Message();
        $data = [
            'field1' => 'value1',
            'field2' => 'value2',
        ];
        $document->setData($data);
        $this->assertEquals($data, $document->getData());
    }

    /**
     * @depends testSetupBody
     */
    public function testParseBody()
    {
        $document = new Message();
        $format = 'testFormat';
        $document->setFormat($format);
        $document->parsers = [
            $format => [
                'class' => ParserUrlEncoded::className()
            ]
        ];
        $content = 'name=value';
        $document->setContent($content);
        $this->assertEquals(['name' => 'value'], $document->getData());
    }

    /**
     * @depends testSetupData
     */
    public function testFormatData()
    {
        $document = new Message();
        $format = 'testFormat';
        $document->setFormat($format);
        $document->formatters = [
            $format => [
                'class' => FormatterUrlEncoded::className()
            ]
        ];

        $data = [
            'name' => 'value',
        ];
        $document->setData($data);
        $this->assertEquals('name=value', $document->getContent());
    }
}