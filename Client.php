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
     * @var string base request URL.
     */
    public $baseUrl;
    /**
     * @var array request object configuration.
     */
    public $requestConfig = [];
    /**
     * @var array response config configuration.
     */
    public $responseConfig = [];

    /**
     * @var Transport|array|string|callable HTTP message transport.
     */
    private $_transport = 'yii\httpclient\TransportCurl';


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
        if (is_object($transport)) {
            $transport->client = $this;
        }
        $this->_transport = $transport;
    }

    /**
     * @return Transport HTTP message transport instance.
     */
    public function getTransport()
    {
        if (!is_object($this->_transport)) {
            /* @var $transport Transport */
            $transport = Yii::createObject($this->_transport);
            $transport->client = $this;
            $this->_transport = $transport;
        }
        return $this->_transport;
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
}