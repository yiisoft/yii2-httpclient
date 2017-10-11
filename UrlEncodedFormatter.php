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
     * @var int URL encoding type.
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
        if (($data = $request->getData()) !== null) {
            $content = http_build_query((array)$data, '', '&', $this->encodingType);
        }

        if (strcasecmp('get', $request->getMethod()) === 0) {
            if (!empty($content)) {
                $request->setFullUrl(null);
                $url = $request->getFullUrl();
                $url .= (strpos($url, '?') === false) ? '?' : '&';
                $url .= $content;
                $request->setFullUrl($url);
            }
            return $request;
        }

        $request->getHeaders()->set('Content-Type', 'application/x-www-form-urlencoded; charset=' . $this->getCharset());

        if (isset($content)) {
            $request->setContent($content);
        }

        return $request;
    }

    /**
     * Determine what $charset, if any we need to set. Default to utf8
     *
     * @var string
     */
    public function getCharset()
    {
        if(empty($this->charset)) {
            if(isset(Yii::$app->charset) && !empty(Yii::$app->charset)) {
                return Yii::$app->charset;
            } else {
                return 'UTF-8';
            }
        } else {
            return $this->charset;
        }

        $this->charset === null ? Yii::$app->charset : $this->charset;
    }
}