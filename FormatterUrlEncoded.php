<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\httpclient;

use yii\base\Object;

/**
 * FormatterUrlEncoded formats HTTP message as 'application/x-www-form-urlencoded'.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
class FormatterUrlEncoded extends Object implements FormatterInterface
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
    public $encodingType = PHP_QUERY_RFC3986;


    /**
     * @inheritdoc
     */
    public function format(MessageInterface $httpDocument)
    {
        $httpDocument->getHeaders()->set('Content-Type', 'application/x-www-form-urlencoded');
        $data = $httpDocument->getData();
        return http_build_query($data, '', '&', $this->encodingType);
    }
}