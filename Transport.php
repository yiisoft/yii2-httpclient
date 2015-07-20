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
     * Composes request headers for the cURL.
     * @param Request $request request instance.
     * @return array headers list.
     */
    protected function composeHeaders($request)
    {
        if (!$request->hasHeaders()) {
            return [];
        }
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
        if (!$request->hasCookies()) {
            return '';
        }
        $parts = [];
        foreach ($request->getCookies() as $cookie) {
            $parts[] = $cookie->name . '=' . urlencode($cookie->value);
        }
        return implode(';', $parts);
    }
}