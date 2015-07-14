<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\httpclient;

use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * TransportCurl sends HTTP messages using [Client URL Library (cURL)](http://php.net/manual/en/book.curl.php)
 *
 * Note: this transport requires PHP 'curl' extension installed.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
class TransportCurl extends Transport
{
    /**
     * @inheritdoc
     */
    public function send($request)
    {
        $curlResource = $this->prepare($request);

        $responseContent = curl_exec($curlResource);
        $responseHeaders = curl_getinfo($curlResource);

        // check cURL error
        $errorNumber = curl_errno($curlResource);
        $errorMessage = curl_error($curlResource);

        curl_close($curlResource);

        if ($errorNumber > 0) {
            throw new Exception('Curl error: #' . $errorNumber . ' - ' . $errorMessage);
        }

        return $this->client->createResponse($responseContent, $responseHeaders);
    }

    /**
     * @inheritdoc
     */
    public function batchSend(array $requests)
    {
        $curlBatchResource = curl_multi_init();

        $curlResources = [];
        foreach ($requests as $key => $request) {
            $curlResource = $this->prepare($request);
            $curlResources[$key] = $curlResource;
            curl_multi_add_handle($curlBatchResource, $curlResource);
        }

        $isRunning = null;

        do {
            // See https://bugs.php.net/bug.php?id=61141
            if (curl_multi_select($curlBatchResource) == -1) {
                usleep(100);
            }
            do {
                $curlExecCode = curl_multi_exec($curlBatchResource, $isRunning);
            } while ($curlExecCode == CURLM_CALL_MULTI_PERFORM);
        } while ($isRunning > 0 && $curlExecCode == CURLM_OK);

        $responseContents = [];
        $responseHeaders = [];
        foreach ($curlResources as $key => $curlResource) {
            $responseHeaders[$key] = curl_getinfo($curlResource);
            $responseContents[$key] = curl_multi_getcontent($curlResource);
            curl_multi_remove_handle($curlBatchResource, $curlResource);
        }

        curl_multi_close($curlBatchResource);

        $responses = [];
        foreach ($requests as $key => $request) {
            $responses[$key] = $this->client->createResponse($responseContents[$key], $responseHeaders[$key]);
        }
        return $responses;
    }

    /**
     * Prepare request for execution, creating cURL resource for it.
     * @param Request $request request instance.
     * @return resource prepared cURL resource.
     */
    protected function prepare($request)
    {
        $curlOptions = ArrayHelper::merge(
            $this->composeCurlOptions($request->getOptions()),
            [
                CURLOPT_HTTPHEADER => $this->composeHeaders($request),
                CURLOPT_RETURNTRANSFER => true,
            ]
        );

        $method = strtoupper($request->getMethod());
        switch ($method) {
            case 'GET':
                $url = $this->composeUrl($request, true);
                break;
            case 'POST':
                $url = $this->composeUrl($request);
                $curlOptions[CURLOPT_POST] = true;
                $curlOptions[CURLOPT_POSTFIELDS] = $request->getContent();
                break;
            case 'HEAD':
                $curlOptions[CURLOPT_CUSTOMREQUEST] = $method;
                $url = $this->composeUrl($request, true);
                break;
            default:
                $url = $this->composeUrl($request);
                $curlOptions[CURLOPT_CUSTOMREQUEST] = $method;
                $curlOptions[CURLOPT_POSTFIELDS] = $request->getContent();
        }

        $curlOptions[CURLOPT_URL] = $url;

        $curlResource = curl_init();
        foreach ($curlOptions as $option => $value) {
            curl_setopt($curlResource, $option, $value);
        }

        return $curlResource;
    }

    /**
     * Composes cURL options from raw request options.
     * @param array $options raw request options.
     * @return array cURL options, in format: [curl_constant => value].
     */
    protected function composeCurlOptions(array $options)
    {
        $curlOptions = [];
        foreach ($options as $key => $value) {
            if (is_int($key)) {
                $curlOptions[$key] = $value;
            } else {
                $constantName = 'CURLOPT_' . strtoupper($key);
                $curlOptions[constant($constantName)] = $value;
            }
        }
        return $curlOptions;
    }
}