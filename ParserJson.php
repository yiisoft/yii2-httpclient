<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\httpclient;

use yii\base\Object;
use yii\helpers\Json;

/**
 * ParserJson parses HTTP message content as JSON.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
class ParserJson extends Object implements ParserInterface
{
    /**
     * @inheritdoc
     */
    public function parse(MessageInterface $httpDocument)
    {
        return Json::decode($httpDocument->getContent());
    }
}