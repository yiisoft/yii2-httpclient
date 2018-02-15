<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\httpclient;

use Psr\Http\Message\ResponseInterface;
use yii\http\Cookie;
use yii\http\HeaderCollection;

/**
 * Response represents HTTP request response.
 *
 * @property bool $isOk Whether response is OK. This property is read-only.
 * @property int $statusCode Status code. This property is read-only.
 * @property string $reasonPhrase the reason phrase to use with the current status code. This property is read-only.
 * @property array|object|null $parsedBody parsed body parameters.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
class Response extends Message implements ResponseInterface
{
    /**
     * @var int status code.
     * @since 2.1.0
     */
    private $_statusCode;
    /**
     * @var string the reason phrase to use with the current status code.
     * @since 2.1.0
     */
    private $_reasonPhrase;
    /**
     * @var array|object|null parsed body parameters.
     * @since 2.1.0
     */
    private $_parsedBody;


    /**
     * Specifies body parameters.
     * @param mixed $data content data fields.
     * @return $this self reference.
     * @since 2.1.0
     */
    public function setParsedBody($data)
    {
        $this->_parsedBody = $data;
        return $this;
    }

    /**
     * Retrieve any parameters provided in the response body.
     * This method parses raw body content and returns its structured representation as array or object.
     * @return array|object|null the deserialized body parameters, `null` in case of empty body content.
     * @since 2.1.0
     */
    public function getParsedBody()
    {
        if ($this->_parsedBody === null) {
            if ($this->hasBody()) {
                $this->_parsedBody = $this->getParser()->parse($this);
            }
        }

        return $this->_parsedBody;
    }

    /**
     * {@inheritdoc}
     */
    public function getCookies()
    {
        $cookieCollection = parent::getCookies();
        if ($cookieCollection->getCount() === 0 && $this->hasHeader('set-cookie')) {
            $cookieStrings = $this->getHeader('set-cookie');
            foreach ($cookieStrings as $cookieString) {
                $cookieCollection->add($this->parseCookie($cookieString));
            }
        }
        return $cookieCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        if ($this->_statusCode === null) {
            if (!$this->hasHeader('http-code')) {
                throw new Exception('Unable to get status code: referred header information is missing.');
            }
            // take into account possible 'follow location'
            $statusCodeHeaders = $this->getHeader('http-code');
            $this->_statusCode = empty($statusCodeHeaders) ? null : (int)end($statusCodeHeaders);
        }

        return $this->_statusCode;
    }

    /**
     * Specifies status code and, optionally, reason phrase.
     * @param int $code the 3-digit integer result code to set.
     * @param string $reasonPhrase the reason phrase to use with the provided status code.
     * @since 2.1.0
     */
    public function setStatus($code, $reasonPhrase = '')
    {
        $this->_statusCode = (int)$code;
        $this->_reasonPhrase = $reasonPhrase;
    }

    /**
     * {@inheritdoc}
     * @since 2.1.0
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        if ($this->getStatusCode() === $code && $this->getReasonPhrase() === $reasonPhrase) {
            return $this;
        }

        $newInstance = clone $this;
        $newInstance->setStatus($code, $reasonPhrase);
        return $newInstance;
    }

    /**
     * {@inheritdoc}
     * @since 2.1.0
     */
    public function getReasonPhrase()
    {
        if (empty($this->_reasonPhrase)) {
            $statusCode = $this->getStatusCode();
            if (isset(\yii\web\Response::$httpStatuses[$statusCode])) {
                $this->_reasonPhrase = \yii\web\Response::$httpStatuses[$statusCode];
            } else {
                $this->_reasonPhrase = 'Unknown';
            }
        }

        return $this->_reasonPhrase;
    }

    /**
     * Checks if response status code is OK (status code = 20x)
     * @return bool whether response is OK.
     */
    public function getIsOk()
    {
        return strncmp('20', $this->getStatusCode(), 2) === 0;
    }

    /**
     * Returns default format automatically detected from headers and content.
     * @return string|null format name, 'null' - if detection failed.
     */
    protected function defaultFormat()
    {
        $format = $this->detectFormatByHeaders($this->getHeaderCollection());
        if ($format === null) {
            $format = $this->detectFormatByContent($this->getContent());
        }

        return $format;
    }

    /**
     * Detects format from headers.
     * @param HeaderCollection $headers source headers.
     * @return null|string format name, 'null' - if detection failed.
     */
    protected function detectFormatByHeaders(HeaderCollection $headers)
    {
        $contentTypeHeaders = $headers->get('content-type', null, false);

        if (!empty($contentTypeHeaders)) {
            $contentType = end($contentTypeHeaders);
            if (stripos($contentType, 'json') !== false) {
                return Client::FORMAT_JSON;
            }
            if (stripos($contentType, 'urlencoded') !== false) {
                return Client::FORMAT_URLENCODED;
            }
            if (stripos($contentType, 'xml') !== false) {
                return Client::FORMAT_XML;
            }
        }

        return null;
    }

    /**
     * Detects response format from raw content.
     * @param string $content raw response content.
     * @return null|string format name, 'null' - if detection failed.
     */
    protected function detectFormatByContent($content)
    {
        if (preg_match('/^\\{.*\\}$/is', $content)) {
            return Client::FORMAT_JSON;
        }
        if (preg_match('/^([^=&])+=[^=&]+(&[^=&]+=[^=&]+)*$/', $content)) {
            return Client::FORMAT_URLENCODED;
        }
        if (preg_match('/^<.*>$/s', $content)) {
            return Client::FORMAT_XML;
        }
        return null;
    }

    /**
     * Parses cookie value string, creating a [[Cookie]] instance.
     * @param string $cookieString cookie header string.
     * @return Cookie cookie object.
     */
    private function parseCookie($cookieString)
    {
        $params = [];
        $pairs = explode(';', $cookieString);
        foreach ($pairs as $number => $pair) {
            $pair = trim($pair);
            if (strpos($pair, '=') === false) {
                $params[$this->normalizeCookieParamName($pair)] = true;
            } else {
                [$name, $value] = explode('=', $pair, 2);
                if ($number === 0) {
                    $params['name'] = $name;
                    $params['value'] = urldecode($value);
                } else {
                    $params[$this->normalizeCookieParamName($name)] = urldecode($value);
                }
            }
        }

        $cookie = new Cookie();
        foreach ($params as $name => $value) {
            if ($cookie->canSetProperty($name)) {
                // Cookie string may contain custom unsupported params
                $cookie->$name = $value;
            }
        }
        return $cookie;
    }

    /**
     * @param string $rawName raw cookie parameter name.
     * @return string name of [[Cookie]] field.
     */
    private function normalizeCookieParamName($rawName)
    {
        static $nameMap = [
            'expires' => 'expire',
            'httponly' => 'httpOnly',
            'max-age' => 'maxAge',
        ];
        $name = strtolower($rawName);
        if (isset($nameMap[$name])) {
            $name = $nameMap[$name];
        }
        return $name;
    }

    /**
     * @return ParserInterface message parser instance.
     * @throws Exception if unable to detect parser.
     */
    private function getParser()
    {
        $format = $this->getFormat();
        if ($format === null) {
            throw new Exception("Unable to detect format for content parsing. Raw response:\n\n" . $this->toString());
        }
        return $this->client->getParser($format);
    }
}