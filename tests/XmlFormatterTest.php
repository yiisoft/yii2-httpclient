<?php

namespace yiiunit\extensions\httpclient;

use DOMDocument;
use DOMElement;
use yii\base\Arrayable;
use yii\httpclient\Request;
use yii\httpclient\XmlFormatter;

class XmlFormatterTest extends TestCase
{
    protected function setUp(): void
    {
        $this->mockApplication();
    }

    // Tests :

    public function testFormat()
    {
        $request = new Request();
        $data = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];
        $request->setData($data);

        $formatter = new XmlFormatter();
        $formatter->format($request);
        $expectedContent = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<request><name1>value1</name1><name2>value2</name2></request>
XML;
        $this->assertEqualsWithoutLE($expectedContent, $request->getContent());
        $this->assertEquals('application/xml; charset=UTF-8', $request->getHeaders()->get('Content-Type'));
    }

    public function testFormatStringData()
    {
        $request = new Request();
        $formatter = new XmlFormatter();
        $request->setData('data');
        $formatter->format($request);
        $expectedContent1 = <<<XML1
<?xml version="1.0" encoding="UTF-8"?>
<request>data</request>
XML1;
        $this->assertEqualsWithoutLE($expectedContent1, $request->getContent());
    }

    /**
     * @depends testFormat
     */
    public function testFormatArrayWithNumericKey()
    {
        $request = new Request();
        $data = [
            'group' => [
                [
                    'name1' => 'value1',
                    'name2' => 'value2',
                ],
            ],
        ];
        $request->setData($data);

        $formatter = new XmlFormatter();
        $formatter->format($request);
        $expectedContent = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<request><group><item><name1>value1</name1><name2>value2</name2></item></group></request>
XML;
        $this->assertEqualsWithoutLE($expectedContent, $request->getContent());
    }

    /**
     * @depends testFormat
     */
    public function testFormatTraversable()
    {
        $request = new Request();

        $postsStack = new \SplStack();
        $post = new \stdClass();
        $post->name = 'name1';
        $postsStack->push($post);

        $request->setData($postsStack);

        $formatter = new XmlFormatter();

        $formatter->useTraversableAsArray = true;
        $formatter->format($request);
        $expectedContent = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<request><stdClass><name>name1</name></stdClass></request>
XML;
        $this->assertEqualsWithoutLE($expectedContent, $request->getContent());

        $formatter->useTraversableAsArray = false;
        $formatter->format($request);
        $expectedContent = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<request><SplStack><stdClass><name>name1</name></stdClass></SplStack></request>
XML;
        $this->assertEqualsWithoutLE($expectedContent, $request->getContent());
    }

    public function testFormatArrayable()
    {
        $request = new Request();

        $postsStack = new \SplStack();
        $post = new ArrayableClass();
        $postsStack->push($post);

        $request->setData($postsStack);

        $formatter = new XmlFormatter();

        $formatter->useTraversableAsArray = true;
        $formatter->format($request);
        $expectedContent = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<request><ArrayableClass><name1>value1</name1></ArrayableClass></request>
XML;
        $this->assertEqualsWithoutLE($expectedContent, $request->getContent());
    }

    /**
     * @depends testFormat
     */
    public function testFormatFromDom()
    {
        $request = new Request();
        $data = new DOMDocument('1.0', 'UTF-8');
        $root = new DOMElement('root');
        $data->appendChild($root);
        $request->setData($data);

        $formatter = new XmlFormatter();
        $formatter->format($request);
        $expectedContent = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<root/>
XML;
        $this->assertEqualsWithoutLE($expectedContent, $request->getContent());
    }

    /**
     * @depends testFormat
     */
    public function testFormatFromSimpleXml()
    {
        $request = new Request();

        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<request><name1>value1</name1><name2>value2</name2></request>
XML;
        $simpleXmlElement = simplexml_load_string($xml);
        $request->setData($simpleXmlElement);

        $formatter = new XmlFormatter();
        $formatter->format($request);
        $this->assertEqualsWithoutLE($xml, $request->getContent());
    }

    /**
     * @depends testFormat
     */
    public function testFormatEmpty()
    {
        $request = new Request();

        $formatter = new XmlFormatter();
        $formatter->format($request);
        $this->assertNull($request->getContent());
    }
}

class ArrayableClass implements Arrayable
{
    public function fields()
    {
    }

    public function extraFields()
    {
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        return ['name1' => 'value1'];
    }
}
