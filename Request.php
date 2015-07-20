<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\httpclient;
use yii\helpers\ArrayHelper;

/**
 * Request represents HTTP request.
 *
 * @property string $url target URL.
 * @property string $method request method.
 * @property array $options request options. See [[setOptions()]] for details.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
class Request extends Message
{
    /**
     * @var string target URL.
     */
    private $_url;
    /**
     * @var string request method.
     */
    private $_method = 'get';
    /**
     * @var array request options.
     */
    private $_options = [];


    /**
     * @param string $url target URL
     * @return $this self reference.
     */
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    /**
     * @return string target URL
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @param string $method request method
     * @return $this self reference.
     */
    public function setMethod($method)
    {
        $this->_method = $method;
        return $this;
    }

    /**
     * @return string request method
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Following options are supported:
     * - timeout: integer, the maximum number of seconds to allow request to be executed.
     * - port: integer, an alternative port number to connect to.
     * - userAgent: string, the contents of the "User-Agent: " header to be used in a HTTP request.
     * - followLocation: boolean, whether to follow any "Location: " header that the server sends as part of the HTTP header.
     * - sslVerifyPeer: boolean, whether verification of the peer's certificate should be performed.
     *
     * You may set options using keys, which are specific to particular transport, like `[CURLOPT_VERBOSE => true]` in case
     * there is a necessity for it.
     *
     * @param array $options request options.
     * @return $this self reference.
     */
    public function setOptions(array $options)
    {
        $this->_options = $options;
        return $this;
    }

    /**
     * @return array request options.
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Adds more options to already defined ones.
     * Please refer to [[setOptions()]] on how to specify options.
     * @param array $options additional options
     * @return $this self reference.
     */
    public function addOptions(array $options)
    {
        $this->options = ArrayHelper::merge($this->options, $options); // `array_merge()` will produce invalid result for cURL options
        return $this;
    }

    /**
     * Prepares this request instance for sending.
     * This method should be invoked by transport before sending a request.
     * Do not call this method unless you know what you are doing.
     * @return $this self reference.
     */
    public function prepare()
    {
        if (!empty($this->client->baseUrl)) {
            $url = $this->getUrl();
            if (!preg_match('/^https?:\\/\\//is', $url)) {
                $this->setUrl($this->client->baseUrl . '/' . $url);
            }
        }
        $this->getFormatter()->format($this);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function composeHeaderLines()
    {
        $headers = parent::composeHeaderLines();
        if ($this->hasCookies()) {
            $parts = [];
            foreach ($this->getCookies() as $cookie) {
                $parts[] = $cookie->name . '=' . urlencode($cookie->value);
            }
            $headers[] = 'Cookie: ' . implode(';', $parts);
        }
        return $headers;
    }

    /**
     * Sends this request.
     * @return Response response instance.
     */
    public function send()
    {
        return $this->client->send($this);
    }

    /**
     * @return FormatterInterface message formatter instance.
     */
    private function getFormatter()
    {
        return $this->client->getFormatter($this->getFormat());
    }
}