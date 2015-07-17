<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\httpclient;

use yii\base\Exception;

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

        $responseHeaders = [];
        $this->setHeaderOutput($curlResource, $responseHeaders);

        $responseContent = curl_exec($curlResource);

        // check cURL error
        $errorNumber = curl_errno($curlResource);
        $errorMessage = curl_error($curlResource);

        curl_close($curlResource);

        if ($errorNumber > 0) {
            throw new Exception('Curl error: #' . $errorNumber . ' - ' . $errorMessage);
        }

        return $this->createResponse($responseContent, $responseHeaders);
    }

    /**
     * @inheritdoc
     */
    public function batchSend(array $requests)
    {
        $curlBatchResource = curl_multi_init();

        $curlResources = [];
        $responseHeaders = [];
        foreach ($requests as $key => $request) {
            $curlResource = $this->prepare($request);
            $responseHeaders[$key] = [];
            $this->setHeaderOutput($curlResource, $responseHeaders[$key]);
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
        foreach ($curlResources as $key => $curlResource) {
            $responseContents[$key] = curl_multi_getcontent($curlResource);
            curl_multi_remove_handle($curlBatchResource, $curlResource);
        }

        curl_multi_close($curlBatchResource);

        $responses = [];
        foreach ($requests as $key => $request) {
            $responses[$key] = $this->createResponse($responseContents[$key], $responseHeaders[$key]);
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
        $request->prepare();

        $curlOptions = $this->composeCurlOptions($request->getOptions());

        $method = strtolower($request->getMethod());
        switch ($method) {
            case 'get':
                break;
            case 'post':
                $curlOptions[CURLOPT_POST] = true;
                break;
            default:
                $curlOptions[CURLOPT_CUSTOMREQUEST] = $method;
        }

        $content = $request->getContent();
        if ($content !== null) {
            $curlOptions[CURLOPT_POSTFIELDS] = $content;
        }

        $curlOptions[CURLOPT_RETURNTRANSFER] = true;
        $curlOptions[CURLOPT_URL] = $request->getUrl();
        $curlOptions[CURLOPT_HTTPHEADER] = $this->composeHeaders($request);
        $curlOptions[CURLOPT_COOKIE] = $this->composeCookies($request);

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
                $key = strtoupper($key);
                if (strpos($key, 'SSL') === 0) {
                    $key = substr($key, 3);
                    $constantName = 'CURLOPT_SSL_' . $key;
                    if (!defined($constantName)) {
                        $constantName = 'CURLOPT_SSL' . $key;
                    }
                } else {
                    $constantName = 'CURLOPT_' . strtoupper($key);
                }
                $curlOptions[constant($constantName)] = $value;
            }
        }
        return $curlOptions;
    }

    /**
     * Setup a variable, which should collect the cURL response headers.
     * @param resource $curlResource cURL resource.
     * @param array $output variable, which should collection headers.
     */
    protected function setHeaderOutput($curlResource, array &$output)
    {
        curl_setopt($curlResource, CURLOPT_HEADERFUNCTION, function($resource, $headerString) use (&$output) {
            $header = trim($headerString, "\n\r");
            if (strlen($header) > 0) {
                $output[] = $header;
            }
            return mb_strlen($headerString, '8bit');
        });
    }
}