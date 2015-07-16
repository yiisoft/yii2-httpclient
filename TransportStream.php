<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\httpclient;

use yii\helpers\ArrayHelper;

/**
 * TransportStream sends HTTP messages using [Streams](http://php.net/manual/en/book.stream.php)
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
class TransportStream extends Transport
{
    /**
     * @inheritdoc
     */
    public function send($request)
    {
        $method = strtoupper($request->getMethod());

        $contextOptions = [
            'http' => [
                'method' => $method,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => false,
            ],
        ];

        switch ($method) {
            case 'GET':
                $url = $this->composeUrl($request, true);
                break;
            case 'POST':
                $url = $this->composeUrl($request);
                $contextOptions['http']['content'] = $request->getContent();
                break;
            case 'HEAD':
                $url = $this->composeUrl($request, true);
                break;
            default:
                $url = $this->composeUrl($request);
                $contextOptions['http']['content'] = $request->getContent();
        }

        $headers = $this->composeHeaders($request);
        $headers[] = 'Cookie: ' . $this->composeCookies($request);
        $contextOptions['http']['header'] = $this->composeHeaders($request);

        $contextOptions = ArrayHelper::merge($contextOptions, $this->composeContextOptions($request->getOptions()));

        $context = stream_context_create($contextOptions);
        $stream = fopen($url, 'rb', false, $context);
        $responseContent = stream_get_contents($stream);
        $metaData = stream_get_meta_data($stream);
        fclose($stream);

        $responseHeaders = isset($metaData['wrapper_data']) ? $metaData['wrapper_data'] : [];

        return $this->createResponse($responseContent, $responseHeaders);
    }

    /**
     * Composes stream context options from raw request options.
     * @param array $options raw request options.
     * @return array stream context options.
     */
    protected function composeContextOptions(array $options)
    {
        $contextOptions = [];
        foreach ($options as $key => $value) {
            $contextOptions['http'][$key] = $value;
        }
        return $contextOptions;
    }
}