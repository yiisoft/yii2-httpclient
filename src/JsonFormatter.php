<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\httpclient;

use yii\base\BaseObject;
use yii\helpers\Json;
use yii\http\MemoryStream;

/**
 * JsonFormatter formats HTTP message as JSON.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
class JsonFormatter extends BaseObject implements FormatterInterface
{
    /**
     * @var int the encoding options. For more details please refer to
     * <http://www.php.net/manual/en/function.json-encode.php>.
     */
    public $encodeOptions = 0;


    /**
     * {@inheritdoc}
     */
    public function format(Request $request)
    {
        $request->setHeader('Content-Type', 'application/json; charset=UTF-8');
        if (($data = $request->getParams()) !== null) {
            $body = new MemoryStream();
            $body->write(Json::encode($request->getParams(), $this->encodeOptions));
            $request->setBody($body);
        }
        return $request;
    }
}