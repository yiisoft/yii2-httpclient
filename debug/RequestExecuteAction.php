<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\httpclient\debug;

use yii\base\Action;
use yii\web\HttpException;
use yii\web\Response;

/**
 * RequestExecuteAction executes HTTP request and passes its result to the browser.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
class RequestExecuteAction extends Action
{
    /**
     * @var HttpClientPanel
     */
    public $panel;

    public function run($seq, $tag)
    {
        $this->controller->loadData($tag);

        $timings = $this->panel->calculateTimings();

        if (!isset($timings[$seq])) {
            throw new HttpException(404, 'Log message not found.');
        }

        $requestInfo = $timings[$seq]['info'];

        $httpRequest = $this->createRequestFromLog($requestInfo);
        $httpResponse = $httpRequest->send();
        $httpResponse->getHeaders()->get('content-type');

        $response = new Response([
            'format' => Response::FORMAT_RAW,
            'content' => $httpResponse->content,
        ]);
        foreach ($httpResponse->getHeaders() as $name => $value) {
            $response->getHeaders()->set($name, $value);
        }

        return $response;
    }

    /**
     * Creates an HTTP request instance from log entry.
     * @param string $requestLog HTTP request log entry
     * @return \yii\httpclient\Request request instance.
     */
    protected function createRequestFromLog($requestLog)
    {
        if (strpos($requestLog, "\n\n")) {
            list($head, $content) = explode("\n\n", $requestLog, 2);
        } else {
            $head = $requestLog;
            $content = null;
        }

        $headers = explode("\n", $head);
        $main = array_shift($headers);
        list($method, $url) = explode(' ', $main, 2);

        return $this->panel->getHttpClient()->createRequest()
            ->setMethod($method)
            ->setUrl($url)
            ->setHeaders($headers)
            ->setContent($content);
    }
}