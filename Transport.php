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
        foreach ($requests as $request) {
            $responses[] = $this->send($request);
        }
        return $responses;
    }

    /**
     * Composes actual request URL string.
     * @param Request $request request instance.
     * @param boolean $appendData whether to append request data to the URL as GET parameters.
     * @return string composed URL.
     */
    protected function composeUrl($request, $appendData = false)
    {
        $url = $request->getUrl();
        if (!empty($this->client->baseUrl)) {
            if (!preg_match('/^https?:\\/\\//is', $url)) {
                $url = $this->client->baseUrl . '/' . $url;
            }
        }

        if ($appendData) {
            $data = $request->getData();
            if (!empty($data)) {
                if (strpos($url, '?') === false) {
                    $url .= '?';
                } else {
                    $url .= '&';
                }
                $url .= http_build_query($data, '', '&', PHP_QUERY_RFC3986);
            }
        }
        return $url;
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
}