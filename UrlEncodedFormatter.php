<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\httpclient;

use Yii;
use yii\base\Object;

/**
 * UrlEncodedFormatter formats HTTP message as 'application/x-www-form-urlencoded'.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
class UrlEncodedFormatter extends Object implements FormatterInterface
{
    /**
     * @var integer URL encoding type.
     * Possible values are:
     *  - PHP_QUERY_RFC1738 - encoding is performed per 'RFC 1738' and the 'application/x-www-form-urlencoded' media type,
     *    which implies that spaces are encoded as plus (+) signs. This is most common encoding type used by most web
     *    applications.
     *  - PHP_QUERY_RFC3986 - then encoding is performed according to 'RFC 3986', and spaces will be percent encoded (%20).
     *    This encoding type is required by OpenID and OAuth protocols.
     */
    public $encodingType = PHP_QUERY_RFC1738;
    /**
     * @var string the content charset. If not set, it will use the value of [[\yii\base\Application::charset]].
     * @since 2.0.1
     */
    public $charset;


    /**
     * @inheritdoc
     */
    public function format(Request $request)
    {
        $data = (array)$request->getData();
        $content = http_build_query($data, '', '&', $this->encodingType);

        if (strcasecmp('get', $request->getMethod()) === 0) {
            if (!empty($content)) {
                $url = $request->getUrl();
                $url .= (strpos($url, '?') === false) ? '?' : '&';
                $url .= $content;
                $request->setUrl($url);
            }
            return $request;
        }

        $charset = $this->charset === null ? Yii::$app->charset : $this->charset;

        $request->getHeaders()->set('Content-Type', 'application/x-www-form-urlencoded; charset=' . $charset);
        $request->setContent($content);
        return $request;
    }
}