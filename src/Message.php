<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\httpclient;

use Psr\Http\Message\MessageInterface;
use yii\base\Component;
use yii\base\ErrorHandler;
use yii\http\Cookie;
use yii\http\CookieCollection;
use Yii;
use yii\http\MemoryStream;
use yii\http\MessageTrait;

/**
 * Message represents a base HTTP message.
 *
 * @property string $content Raw body.
 * @property CookieCollection|Cookie[] $cookies The cookie collection. Note that the type of this property
 * differs in getter and setter. See [[getCookies()]] and [[setCookies()]] for details.
 * @property mixed $data Content data fields.
 * @property string $format Body format name.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
class Message extends Component implements MessageInterface
{
    use MessageTrait;

    /**
     * @var Client owner client instance.
     */
    public $client;

    /**
     * @var CookieCollection cookies.
     */
    private $_cookies;
    /**
     * @var mixed content data
     */
    private $_data;
    /**
     * @var string content format name
     */
    private $_format;


    /**
     * Sets up message's headers at batch, removing any previously existing ones.
     * @param string[][] $headers an associative array of the message's headers.
     */
    public function setHeaders($headers)
    {
        // @todo move to other place, restoring `MessageTrait::setHeaders()`

        $headerCollection = $this->getHeaderCollection();
        $headerCollection->removeAll();

        foreach ($headers as $name => $value) {
            if (is_int($name)) {
                // parse raw header :
                $rawHeader = $value;
                if (strpos($rawHeader, 'HTTP/') === 0) {
                    $parts = explode(' ', $rawHeader, 3);
                    $headerCollection->add('http-code', $parts[1]);
                } elseif (($separatorPos = strpos($rawHeader, ':')) !== false) {
                    $name = strtolower(trim(substr($rawHeader, 0, $separatorPos)));
                    $value = trim(substr($rawHeader, $separatorPos + 1));
                    $headerCollection->add($name, $value);
                } else {
                    $headerCollection->add('raw', $rawHeader);
                }
            } else {
                $headerCollection->set($name, $value);
            }
        }
    }

    /**
     * Sets the cookies associated with HTTP message.
     * @param CookieCollection|Cookie[]|array $cookies cookie collection or cookies list.
     * @return $this self reference.
     */
    public function setCookies($cookies)
    {
        $this->_cookies = $cookies;
        return $this;
    }

    /**
     * Returns the cookie collection.
     * The cookie collection contains the cookies associated with HTTP message.
     * @return CookieCollection|Cookie[] the cookie collection.
     */
    public function getCookies()
    {
        if (!is_object($this->_cookies)) {
            $cookieCollection = new CookieCollection();
            if (is_array($this->_cookies)) {
                foreach ($this->_cookies as $cookie) {
                    if (!is_object($cookie)) {
                        $cookie = new Cookie($cookie);
                    }
                    $cookieCollection->add($cookie);
                }
            }
            $this->_cookies = $cookieCollection;
        }
        return $this->_cookies;
    }

    /**
     * Adds more cookies to the already defined ones.
     * @param Cookie[]|array $cookies additional cookies.
     * @return $this self reference.
     */
    public function addCookies(array $cookies)
    {
        $cookieCollection = $this->getCookies();
        foreach ($cookies as $cookie) {
            if (!is_object($cookie)) {
                $cookie = new Cookie($cookie);
            }
            $cookieCollection->add($cookie);
        }
        return $this;
    }

    /**
     * Checks of HTTP message contains any cookie.
     * Using this method you are able to check cookie presence without instantiating [[CookieCollection]].
     * @return bool whether message contains any cookie.
     */
    public function hasCookies()
    {
        if (is_object($this->_cookies)) {
            return $this->_cookies->getCount() > 0;
        }
        return !empty($this->_cookies);
    }

    /**
     * Sets the HTTP message raw content.
     * @param string $content raw content.
     * @return $this self reference.
     */
    public function setContent($content)
    {
        $body = new MemoryStream();
        $body->write($content);
        $this->setBody($body);
        return $this;
    }

    /**
     * Returns HTTP message raw content.
     * @return string raw body.
     */
    public function getContent()
    {
        return $this->getBody()->__toString();
    }

    /**
     * Sets the data fields, which composes message content.
     * @param mixed $data content data fields.
     * @return $this self reference.
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * Returns the data fields, parsed from raw content.
     * @return mixed content data fields.
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Adds data fields to the existing ones.
     * @param array $data additional content data fields.
     * @return $this self reference.
     * @since 2.0.1
     */
    public function addData($data)
    {
        if (empty($this->_data)) {
            $this->_data = $data;
        } else {
            $this->_data = array_merge($this->_data, $data);
        }
        return $this;
    }

    /**
     * Sets body format.
     * @param string $format body format name.
     * @return $this self reference.
     */
    public function setFormat($format)
    {
        $this->_format = $format;
        return $this;
    }

    /**
     * Returns body format.
     * @return string body format name.
     */
    public function getFormat()
    {
        if ($this->_format === null) {
            $this->_format = $this->defaultFormat();
        }
        return $this->_format;
    }

    /**
     * Returns default format name.
     * @return string default format name.
     */
    protected function defaultFormat()
    {
        return Client::FORMAT_URLENCODED;
    }

    /**
     * Composes raw header lines from [[headers]].
     * Each line will be a string in format: 'header-name: value'.
     * @return array raw header lines.
     */
    public function composeHeaderLines()
    {
        $headers = [];
        foreach ($this->getHeaders() as $name => $values) {
            $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
            foreach ($values as $value) {
                $headers[] = "$name: $value";
            }
        }
        return $headers;
    }

    /**
     * Returns string representation of this HTTP message.
     * @return string the string representation of this HTTP message.
     */
    public function toString()
    {
        $headers = $this->composeHeaderLines();
        $result = implode("\n", $headers);

        $content = $this->getContent();
        if ($content !== null) {
            $result .= "\n\n" . $content;
        }

        return $result;
    }

    /**
     * PHP magic method that returns the string representation of this object.
     * @return string the string representation of this object.
     */
    public function __toString()
    {
        // __toString cannot throw exception
        // use trigger_error to bypass this limitation
        try {
            return $this->toString();
        } catch (\Exception $e) {
            ErrorHandler::convertExceptionToError($e);
            return '';
        }
    }
}