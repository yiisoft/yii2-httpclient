<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\httpclient;

use yii\base\Exception;
use yii\base\Component;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\StringHelper;

/**
 * Client provide high level interface for HTTP requests execution.
 *
 * @property Transport|array|string|callable $transport HTTP message transport, see [[setTransport()]] for details.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
class Client extends Component
{
    /**
     * JSON format
     */
    const FORMAT_JSON = 'json';
    /**
     * urlencoded by RFC1738 query string, like name1=value1&name2=value2
     * @see http://php.net/manual/en/function.urlencode.php
     */
    const FORMAT_URLENCODED = 'urlencoded';
    /**
     * urlencoded by PHP_QUERY_RFC3986 query string, like name1=value1&name2=value2
     * @see http://php.net/manual/en/function.rawurlencode.php
     */
    const FORMAT_RAW_URLENCODED = 'raw-urlencoded';
    /**
     * XML format
     */
    const FORMAT_XML = 'xml';

    /**
     * @var string base request URL.
     */
    public $baseUrl;
    /**
     * @var array the formatters for converting data into the content of the specified [[format]].
     * The array keys are the format names, and the array values are the corresponding configurations
     * for creating the formatter objects.
     */
    public $formatters = [];
    /**
     * @var array the parsers for converting content of the specified [[format]] into the data.
     * The array keys are the format names, and the array values are the corresponding configurations
     * for creating the parser objects.
     */
    public $parsers = [];
    /**
     * @var array request object configuration.
     */
    public $requestConfig = [];
    /**
     * @var array response config configuration.
     */
    public $responseConfig = [];
    /**
     * @var integer maximum symbols count of the request content, which should be taken to compose a
     * log and profile messages. Exceeding content will be truncated.
     * @see createRequestLogToken()
     */
    public $contentLoggingMaxSize = 2000;

    /**
     * @var Transport|array|string|callable HTTP message transport.
     */
    private $_transport = 'yii\httpclient\TransportStream';


    /**
     * Sets the HTTP message transport. It can be specified in one of the following forms:
     *
     * - an instance of `Transport`: actual transport object to be used
     * - a string: representing the class name of the object to be created
     * - a configuration array: the array must contain a `class` element which is treated as the object class,
     *   and the rest of the name-value pairs will be used to initialize the corresponding object properties
     * - a PHP callable: either an anonymous function or an array representing a class method (`[$class or $object, $method]`).
     *   The callable should return a new instance of the object being created.
     * @param Transport|array|string $transport HTTP message transport
     */
    public function setTransport($transport)
    {
        $this->_transport = $transport;
    }

    /**
     * @return Transport HTTP message transport instance.
     */
    public function getTransport()
    {
        if (!is_object($this->_transport)) {
            $this->_transport = Yii::createObject($this->_transport);
        }
        return $this->_transport;
    }

    /**
     * Returns HTTP message formatter instance for the specified format.
     * @param string $format format name.
     * @return FormatterInterface formatter instance.
     * @throws InvalidParamException on invalid format name.
     */
    public function getFormatter($format)
    {
        static $defaultFormatters = [
            self::FORMAT_JSON => 'yii\httpclient\FormatterJson',
            self::FORMAT_URLENCODED => [
                'class' => 'yii\httpclient\FormatterUrlEncoded',
                'encodingType' => PHP_QUERY_RFC1738
            ],
            self::FORMAT_RAW_URLENCODED => [
                'class' => 'yii\httpclient\FormatterUrlEncoded',
                'encodingType' => PHP_QUERY_RFC3986
            ],
            self::FORMAT_XML => 'yii\httpclient\FormatterXML',
        ];

        if (!isset($this->formatters[$format])) {
            if (!isset($defaultFormatters[$format])) {
                throw new InvalidParamException("Unrecognized format '{$format}'");
            }
            $this->formatters[$format] = $defaultFormatters[$format];
        }

        if (!is_object($this->formatters[$format])) {
            $this->formatters[$format] = Yii::createObject($this->formatters[$format]);
        }

        return $this->formatters[$format];
    }

    /**
     * Returns HTTP message parser instance for the specified format.
     * @param string $format format name
     * @return ParserInterface parser instance.
     * @throws InvalidParamException on invalid format name.
     */
    public function getParser($format)
    {
        static $defaultParsers = [
            self::FORMAT_JSON => 'yii\httpclient\ParserJson',
            self::FORMAT_URLENCODED => 'yii\httpclient\ParserUrlEncoded',
            self::FORMAT_RAW_URLENCODED => 'yii\httpclient\ParserUrlEncoded',
            self::FORMAT_XML => 'yii\httpclient\ParserXml',
        ];

        if (!isset($this->parsers[$format])) {
            if (!isset($defaultParsers[$format])) {
                throw new InvalidParamException("Unrecognized format '{$format}'");
            }
            $this->parsers[$format] = $defaultParsers[$format];
        }

        if (!is_object($this->parsers[$format])) {
            $this->parsers[$format] = Yii::createObject($this->parsers[$format]);
        }

        return $this->parsers[$format];
    }

    /**
     * @return Request request instance.
     */
    public function createRequest()
    {
        $config = $this->requestConfig;
        if (!isset($config['class'])) {
            $config['class'] = Request::className();
        }
        $config['client'] = $this;
        return Yii::createObject($config);
    }

    /**
     * Creates a response instance.
     * @param string $content raw content
     * @param array $headers headers list.
     * @return Response request instance.
     */
    public function createResponse($content = null, array $headers = [])
    {
        $config = $this->responseConfig;
        if (!isset($config['class'])) {
            $config['class'] = Response::className();
        }
        $config['client'] = $this;
        $response = Yii::createObject($config);
        $response->setContent($content);
        $response->setHeaders($headers);
        return $response;
    }

    /**
     * Performs given request.
     * @param Request $request request to be sent.
     * @return Response response instance.
     * @throws Exception on failure.
     */
    public function send($request)
    {
        return $this->getTransport()->send($request);
    }

    /**
     * Performs multiple HTTP requests in parallel.
     * @param Request[] $requests requests to perform.
     * @return Response[] responses list.
     */
    public function batchSend(array $requests)
    {
        return $this->getTransport()->batchSend($requests);
    }

    /**
     * Composes the log/profiling message token for the given HTTP request parameters.
     * This method should be used by transports during request sending logging.
     * @param string $method request method name.
     * @param string $url request URL.
     * @param array $headers request headers.
     * @param string $content request content.
     * @return string log token.
     */
    public function createRequestLogToken($method, $url, $headers, $content)
    {
        $token = strtoupper($method) . ' ' . $url;
        if (!empty($headers)) {
            $token .= "\n" . implode("\n", (array)$headers);
        }
        if ($content !== null) {
            $token .= "\n\n" . StringHelper::truncate($content, $this->contentLoggingMaxSize);
        }
        return $token;
    }

    // Create request shortcut methods :

    /**
     * Creates 'GET' request.
     * @param string $url target URL.
     * @param array|string $data if array - request data, otherwise - request content.
     * @param array $headers request headers.
     * @param array $options request options.
     * @return Request request instance.
     */
    public function get($url, $data = null, $headers = [], $options = [])
    {
        return $this->createRequestShortcut('get', $url, $data, $headers, $options);
    }

    /**
     * Creates 'POST' request.
     * @param string $url target URL.
     * @param array|string $data if array - request data, otherwise - request content.
     * @param array $headers request headers.
     * @param array $options request options.
     * @return Request request instance.
     */
    public function post($url, $data = null, $headers = [], $options = [])
    {
        return $this->createRequestShortcut('post', $url, $data, $headers, $options);
    }

    /**
     * Creates 'PUT' request.
     * @param string $url target URL.
     * @param array|string $data if array - request data, otherwise - request content.
     * @param array $headers request headers.
     * @param array $options request options.
     * @return Request request instance.
     */
    public function put($url, $data = null, $headers = [], $options = [])
    {
        return $this->createRequestShortcut('put', $url, $data, $headers, $options);
    }

    /**
     * Creates 'PATCH' request.
     * @param string $url target URL.
     * @param array|string $data if array - request data, otherwise - request content.
     * @param array $headers request headers.
     * @param array $options request options.
     * @return Request request instance.
     */
    public function patch($url, $data = null, $headers = [], $options = [])
    {
        return $this->createRequestShortcut('patch', $url, $data, $headers, $options);
    }

    /**
     * Creates 'DELETE' request.
     * @param string $url target URL.
     * @param array|string $data if array - request data, otherwise - request content.
     * @param array $headers request headers.
     * @param array $options request options.
     * @return Request request instance.
     */
    public function delete($url, $data = null, $headers = [], $options = [])
    {
        return $this->createRequestShortcut('delete', $url, $data, $headers, $options);
    }

    /**
     * Creates 'HEAD' request.
     * @param string $url target URL.
     * @param array $headers request headers.
     * @param array $options request options.
     * @return Request request instance.
     */
    public function head($url, $headers = [], $options = [])
    {
        return $this->createRequestShortcut('head', $url, null, $headers, $options);
    }

    /**
     * Creates 'OPTIONS' request.
     * @param string $url target URL.
     * @param array $options request options.
     * @return Request request instance.
     */
    public function options($url, $options = [])
    {
        return $this->createRequestShortcut('options', $url, null, [], $options);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array|string $data
     * @param array $headers
     * @param array $options
     * @return Request request instance.
     */
    private function createRequestShortcut($method, $url, $data, $headers, $options)
    {
        $request = $this->createRequest()
            ->setMethod($method)
            ->setUrl($url)
            ->addHeaders($headers)
            ->addOptions($options);
        if (is_array($data)) {
            $request->setData($data);
        } else {
            $request->setContent($data);
        }
        return $request;
    }
}
