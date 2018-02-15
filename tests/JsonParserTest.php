<?php

namespace yiiunit\httpclient;

use yii\helpers\Json;
use yii\http\MemoryStream;
use yii\httpclient\JsonParser;
use yii\httpclient\Response;

class JsonParserTest extends TestCase
{
    public function testParse()
    {
        $response = new Response();
        $data = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];
        $response->getBody()->write(Json::encode($data));

        $parser = new JsonParser();
        $this->assertEquals($data, $parser->parse($response));
    }

    public function testParse2()
    {
        $response = new Response();
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
        $response->getBody()->write(<<<JSON
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
        $this->assertEquals($data, $parser->parse($response));
    }
}