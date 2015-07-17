<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\httpclient;

/**
 * Request represents HTTP request.
 *
 * @property string $url target URL.
 * @property string $method request method.
 * @property array $options request options.
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
     * @var array CURL options
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