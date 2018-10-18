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

    public function testParseAsObject()
    {
        $document = new Response();
        $data = new \stdClass();
        $data->name1 = 'value1';
        $data->name2 = 'value2';
        $document->setContent(Json::encode($data));

        $parser = new JsonParser([
            'asArray' => false,
        ]);
        $this->assertEquals($data, $parser->parse($document));
    }

    public function testParse2()
    {
        $document = new Response();
        $data = [
            'code' => 412,
            'httpMessage' => 'Precondition Failed',
            'message' => 'Request Active',
            'data' => [
                'requestId' => 10,
                'userId' => '17',
                'registryDate' => '2016-08-19 21:54:40',
                'contractedServiceData' => [
                        'id' => 6,
                        'status' => true,
                    ],
            ],
            'errors' => null,
        ];
        $document->setContent(<<<JSON
{
  "code": 412,
  "httpMessage": "Precondition Failed",
  "message": "Request Active",
  "data": {
    "requestId": 10,
    "userId": "17",
    "registryDate": "2016-08-19 21:54:40",
    "contractedServiceData": {
      "id": 6,
      "status": true
    }
  },
  "errors": null
}
JSON
        );


        $parser = new JsonParser();
        $this->assertEquals($data, $parser->parse($document));
    }
}