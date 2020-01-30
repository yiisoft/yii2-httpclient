<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\httpclient;

use yii\base\BaseObject;
use yii\helpers\Json;

/**
 * JsonParser parses HTTP message content as JSON.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
class JsonParser extends BaseObject implements ParserInterface
{
    /**
     * @var bool whether to return objects in terms of associative arrays.
     * @since 2.0.8
     */
    public $asArray = true;


    /**
     * {@inheritdoc}
     */
    public function parse(Response $response)
    {
        $content = $response->getContent();
        if(mb_detect_encoding($content) == 'UTF-8') {
            $content = preg_replace('/[^(\x20-\x7F)]*/','', $content);    
        }
        
        return Json::decode($content, $this->asArray);
    }
}
