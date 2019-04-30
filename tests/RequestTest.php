<?php

namespace yiiunit\extensions\httpclient;

use yii\httpclient\Client;
use yii\httpclient\Request;

class RequestTest extends TestCase
{
    protected function setUp()
    {
        $this->mockApplication();
    }

    // Tests :

    public function testSetupUrl()
    {
        $request = new Request();

        $url = 'test/url';
        $request->setUrl($url);
        $this->assertEquals($url, $request->getUrl());
    }

    public function testAddData()
    {
        $request = new Request([
            'client' => new Client(),
            'format' => Client::FORMAT_URLENCODED,
            'method' => 'POST',
        ]);

        $data1 = [
            'name1' => 'value1',
        ];

        $data2 = [
            'name2' => 'value2',
        ];

        $data3 = [
            'name3' => 'value3',
        ];

        $request->addData($data1);
        $this->assertEquals($data1, $request->getData());
        $request->addData($data2);
        $this->assertEquals(array_merge($data1, $data2), $request->getData());
        $request->prepare();
        $request->addData($data3);
        $this->assertEquals(array_merge($data1, $data2, $data3), $request->getData());
    }

    public function testAddFile()
    {
        $request = new Request();
        $request->addFile('file1', __DIR__ . '/test_file.txt');
        $expectedContent = [
            'file1' =>
                [
                    'mimeType' => 'text/plain',
                    'fileName' => 'test_file.txt',
                    'content' => 'file content',
                ],
        ];
        $this->assertEquals($expectedContent, $request->getContent());
    }

    public function testSetupMethod()
    {
        $request = new Request();

        $method = 'PUT';
        $request->setMethod($method);
        $this->assertEquals($method, $request->getMethod());
    }

    public function testSetupOptions()
    {
        $request = new Request();

        $options = [
            'timeout' => 10,
            'userAgent' => 'test',
        ];
        $request->setOptions($options);
        $this->assertEquals($options, $request->getOptions());
    }

    /**
     * @depends testSetupOptions
     */
    public function testAddOptions()
    {
        $request = new Request();

        $options = [
            'timeout' => 10,
            'userAgent' => 'test',
        ];
        $request->setOptions($options);

        $request->addOptions([
            'userAgent' => 'override',
            'test' => 'new',
        ]);

        $expectedOptions = [
            'timeout' => 10,
            'userAgent' => 'override',
            'test' => 'new',
        ];
        $this->assertEquals($expectedOptions, $request->getOptions());

        // merging stream options :
        $request = new Request();
        $request->setOptions([
            'http' => [
                'method' => 'GET',
                'ignore_errors' => true,
            ],
        ]);
        $request->addOptions([
            'http' => [
                'method' => 'POST',
            ],
            'ssl' => [
                'verify_peer' => true,
            ],
        ]);
        $expectedOptions = [
            'http' => [
                'method' => 'POST',
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => true,
            ],
        ];
        $this->assertEquals($expectedOptions, $request->getOptions());

        // merging CURL options :
        $request = new Request();
        $request->setOptions([
            52 => true,
            58 => false,
        ]);
        $request->addOptions([
            'timeout' => 300,
            10006 => '',
            52 => true,
            58 => true,
        ]);
        $expectedOptions = [
            'timeout' => 300,
            10006 => '',
            52 => true,
            58 => true,
        ];
        $this->assertEquals($expectedOptions, $request->getOptions());
    }

    /**
     * @depends testSetupMethod
     */
    public function testFormatData()
    {
        $request = new Request([
            'client' => new Client(),
            'format' => Client::FORMAT_URLENCODED,
            'method' => 'POST',
        ]);

        $data = [
            'name' => 'value',
        ];
        $request->setData($data);
        $request->prepare();
        $this->assertEquals('name=value', $request->getContent());
    }

    /**
     * @depends testFormatData
     */
    public function testToString()
    {
        $request = new Request([
            'client' => new Client(),
            'format' => Client::FORMAT_URLENCODED,
            'method' => 'POST',
            'url' => 'http://domain.com/test',
        ]);

        $data = [
            'name' => 'value',
        ];
        $request->setData($data);

        $expectedResult = <<<EOL
POST http://domain.com/test
Content-Type: application/x-www-form-urlencoded; charset=UTF-8

name=value
EOL;
        $this->assertEqualsWithoutLE($expectedResult, $request->toString());

        // @see https://github.com/yiisoft/yii2-httpclient/issues/70
        $request = new Request([
            'client' => new Client(),
            'format' => Client::FORMAT_URLENCODED,
            'method' => 'POST',
            'url' => 'http://domain.com/test',
        ]);
        $request->setData($data);
        $request->addFileContent('some-file', 'some content');

        $result = $request->toString();
        $this->assertContains('Content-Type: multipart/form-data; boundary=', $result);
        $this->assertContains('some content', $result);
    }

    /**
     * Data provider for [[testGetFullUrl()]]
     * @return array test data
     */
    public function dataProviderGetFullUrl()
    {
        return [
            [
                'http://some-domain.com',
                'test/url',
                'http://some-domain.com/test/url'
            ],
            [
                'http://some-domain.com',
                'http://another-domain.com/test',
                'http://another-domain.com/test',
            ],
            [
                'http://some-domain.com',
                ['test/url', 'param1' => 'name1'],
                'http://some-domain.com/test/url?param1=name1'
            ],
            [
                'http://some-domain.com?base-param=base',
                null,
                'http://some-domain.com?base-param=base',
            ],
            [
                'http://some-domain.com?base-param=base',
                ['param1' => 'name1'],
                'http://some-domain.com?base-param=base&param1=name1',
            ],
            [
                'http://some-domain.com/',
                '/test/url',
                'http://some-domain.com/test/url'
            ],
            [
                'http://some-domain.com/',
                'test/url',
                'http://some-domain.com/test/url'
            ],
            [
                'http://some-domain.com',
                '/test/url',
                'http://some-domain.com/test/url'
            ],
        ];
    }

    /**
     * @depends      testSetupUrl
     * @dataProvider dataProviderGetFullUrl
     *
     * @param string $baseUrl
     * @param mixed $url
     * @param string $expectedFullUrl
     */
    public function testGetFullUrl($baseUrl, $url, $expectedFullUrl)
    {
        $client = new Client();
        $client->baseUrl = $baseUrl;
        $request = new Request(['client' => $client]);

        $request->setUrl($url);
        $this->assertEquals($expectedFullUrl, $request->getFullUrl());
    }

    /**
     * @depends testToString
     */
    public function testReuse()
    {
        $request = new Request([
            'client' => new Client(),
            'format' => Client::FORMAT_URLENCODED,
            'method' => 'POST',
            'url' => 'http://domain.com/test',
        ]);

        $data = [
            'param1' => 'value1',
        ];
        $request->setData($data);

        $expectedResult = <<<EOL
POST http://domain.com/test
Content-Type: application/x-www-form-urlencoded; charset=UTF-8

param1=value1
EOL;
        $this->assertEqualsWithoutLE($expectedResult, $request->toString());

        $data = [
            'param2' => 'value2',
        ];
        $request->setData($data);

        $expectedResult = <<<EOL
POST http://domain.com/test
Content-Type: application/x-www-form-urlencoded; charset=UTF-8

param2=value2
EOL;
        $this->assertEqualsWithoutLE($expectedResult, $request->toString());
    }

    public function testMultiPartRequest()
    {
        $request = new Request([
            'client' => new Client([
                'baseUrl' => '/api'
            ]),
            'method' => 'POST',
        ]);

        $request->setData(['data1' => 'data1=123']);
        $request->addContent('data2', 'data2=456', ['contentType' => 'text/plain']);
        $request->addFileContent('data3', 'file1', ['fileName' => 'file1.txt']);
        $request->addFileContent('data4', 'file2', ['fileName' => 'file2.txt', 'mimeType' => 'text/plain']);
        $this->assertEquals([
            'data2' => [
                'content' => 'data2=456',
                'contentType' => 'text/plain',
            ],
            'data3' => [
                'content' => 'file1',
                'fileName' => 'file1.txt',
                'mimeType' => 'application/octet-stream'
            ],
            'data4' => [
                'content' => 'file2',
                'fileName' => 'file2.txt',
                'mimeType' => 'text/plain',
            ],
        ], $request->getContent());

        $request->prepare();

        $requestString = $request->toString();
        $this->assertTrue((bool)preg_match('~Content-Type: multipart/form-data; boundary=([\w-]+)\n.*\1~s', $requestString, $matches));
        $boundary = $matches[1];
        $parts = explode("--$boundary", $requestString);
        $this->assertCount(6, $parts);
        $expectedPart1 = <<<PART1

Content-Disposition: form-data; name="data1"

data1=123

PART1;
        $this->assertEqualsWithoutLE($expectedPart1, $parts[1]);
        $expectedPart2 = <<<PART2

Content-Disposition: form-data; name="data2"
Content-Type: text/plain

data2=456

PART2;
        $this->assertEqualsWithoutLE($expectedPart2, $parts[2]);
        $expectedPart3 = <<<PART3

Content-Disposition: form-data; name="data3"; filename="file1.txt"
Content-Type: application/octet-stream

file1

PART3;
        $this->assertEqualsWithoutLE($expectedPart3, $parts[3]);
        $expectedPart4 = <<<PART4

Content-Disposition: form-data; name="data4"; filename="file2.txt"
Content-Type: text/plain

file2

PART4;
        $this->assertEqualsWithoutLE($expectedPart4, $parts[4]);
    }

    public function testMultiPartRequestWithParamsWithTheSameNames()
    {
        $request = new Request([
            'client' => new Client([
                'baseUrl' => '/api'
            ]),
            'method' => 'POST',
        ]);

        $request->addContent('x', '1');
        $request->addContent('x', '2');
        $request->addContent('y', '3');
        $request->addContent('z', '4');
        $this->assertCount(4, $request->getContent());

        $request->prepare();

        $requestString = $request->toString();
        $this->assertTrue((bool)preg_match('~Content-Type: multipart/form-data; boundary=([\w-]+)\n.*\1~s', $requestString, $matches));
        $boundary = $matches[1];
        $parts = explode("--$boundary", $requestString);
        $this->assertCount(6, $parts);
        $expectedPart1 = <<<PART1

Content-Disposition: form-data; name="x"

1

PART1;
        $this->assertEqualsWithoutLE($expectedPart1, $parts[1]);
        $expectedPart2 = <<<PART2

Content-Disposition: form-data; name="x"

2

PART2;
        $this->assertEqualsWithoutLE($expectedPart2, $parts[2]);
        $expectedPart3 = <<<PART3

Content-Disposition: form-data; name="y"

3

PART3;
        $this->assertEqualsWithoutLE($expectedPart3, $parts[3]);
        $expectedPart4 = <<<PART4

Content-Disposition: form-data; name="z"

4

PART4;
        $this->assertEqualsWithoutLE($expectedPart4, $parts[4]);
    }

    public function testMultiPartRequestWithFormInputs()
    {
        $request = new Request([
            'client' => new Client([
                'baseUrl' => '/api'
            ]),
            'method' => 'POST',
        ]);

        $formData = ['form' => [
            'name1' => 'value1',
            'name2' => 'value2',
        ]];

        $request->addContent('name', 'content');
        $request->addData($formData);

        $request->prepare();

        $requestString = $request->toString();
        $this->assertTrue((bool)preg_match('~Content-Type: multipart/form-data; boundary=([\w-]+)\n.*\1~s', $requestString, $matches));
        $boundary = $matches[1];
        $parts = explode("--$boundary", $requestString);
        $this->assertCount(5, $parts);
        $expectedPart1 = <<<PART1

Content-Disposition: form-data; name="form[name1]"

value1

PART1;
        $this->assertEqualsWithoutLE($expectedPart1, $parts[1]);
        $expectedPart2 = <<<PART2

Content-Disposition: form-data; name="form[name2]"

value2

PART2;
        $this->assertEqualsWithoutLE($expectedPart2, $parts[2]);
        $expectedPart3 = <<<PART3

Content-Disposition: form-data; name="name"

content

PART3;
        $this->assertEqualsWithoutLE($expectedPart3, $parts[3]);
    }

    /**
     * @see https://github.com/yiisoft/yii2-httpclient/issues/88
     *
     * @depends testToString
     */
    public function testGetParamsReuse()
    {
        $request = new Request([
            'client' => new Client([
                'baseUrl' => 'http://php.net',
            ]),
            'format' => Client::FORMAT_URLENCODED,
            'method' => 'GET',
            'url' => 'docs.php',
            'data' => [
                'example' => '123',
            ],
        ]);

        $this->assertEquals('GET http://php.net/docs.php?example=123', $request->toString());
        $request->prepare();
        $this->assertEquals('GET http://php.net/docs.php?example=123', $request->toString());
    }

    public function testComposeCookieHeader()
    {
        $request = new Request();
        $request->setCookies([
            [
                'name' => 'some',
                'value' => 'foo',
            ]
        ]);
        $headers = $request->composeHeaderLines();
        $this->assertEquals(['Cookie: some=foo'], $headers);

        // @see https://github.com/yiisoft/yii2-httpclient/issues/118
        $request->setCookies([
            [
                'name' => "invalid/name",
                'value' => 'foo',
            ]
        ]);
        $headers = $request->composeHeaderLines();
        $this->assertEquals(['Cookie: invalid/name=foo'], $headers);
    }
}
