<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\httpclient;

use yii\base\Component;
use yii\base\Exception;

/**
 * Transport performs actual HTTP request sending.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
abstract class Transport extends Component
{
    /**
     * @var Client owner client instance.
     */
    public $client;


    /**
     * Performs given request.
     * @param Request $request request to be sent.
     * @return Response response instance.
     * @throws Exception on failure.
     */
    abstract public function send($request);

    /**
     * Performs multiple HTTP requests.
     * Particular transport may benefit from this method, allowing sending requests in parallel.
     * @param Request[] $requests requests to perform.
     * @return Response[] responses list.
     */
    public function batchSend(array $requests)
    {
        $responses = [];
        foreach ($requests as $key => $request) {
            $responses[$key] = $this->send($request);
        }
        return $responses;
    }

    /**
     * Creates a response instance.
     * @param string $content raw content
     * @param array $headers headers list.
     * @return Response request instance.
     */
    protected function createResponse($content, $headers)
    {
        return $this->client->createResponse($content, $this->normalizeResponseHeaders($headers));
    }

    /**
     * Composes request headers for the cURL.
     * @param Request $request request instance.
     * @return array headers list.
     */
    protected function composeHeaders($request)
    {
        $headers = [];
        foreach ($request->getHeaders() as $name => $values) {
            $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
            foreach ($values as $value) {
                $headers[] = "$name: $value";
            }
        }
        return $headers;
    }

    /**
     * Composes request cookies value.
     * @param Request $request request instance.
     * @return string cookies value.
     */
    protected function composeCookies($request)
    {
        $parts = [];
        foreach ($request->getCookies() as $cookie) {
            $parts[] = $cookie->name . '=' . urlencode($cookie->value);
        }
        return implode(';', $parts);
    }

    /**
     * Normalizes response headers.
     * @param array $rawHeaders raw headers in format: name => value.
     * @return array normalized headers.
     */
    protected function normalizeResponseHeaders($rawHeaders)
    {
        $headers = [];
        foreach ($rawHeaders as $rawHeader) {
            if (($separatorPos = strpos($rawHeader, ':')) !== false) {
                $name = strtolower(trim(substr($rawHeader, 0, $separatorPos)));
                $value = trim(substr($rawHeader, $separatorPos + 1));
                if (isset($headers[$name])) {
                    $headers[$name] = (array)$headers[$name];
                    $headers[$name][] = $value;
                } else {
                    $headers[$name] = $value;
                }
            } elseif (strpos($rawHeader, 'HTTP/') === 0) {
                $parts = explode(' ', $rawHeader, 3);
                $headers['http-code'] = $parts[1];
            } else {
                $headers[] = $rawHeader;
            }
        }
        return $headers;
    }
}