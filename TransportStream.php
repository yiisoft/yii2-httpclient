<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\httpclient;

use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

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
        $request->prepare();

        $url = $request->getUrl();
        $method = strtolower($request->getMethod());

        $contextOptions = [
            'http' => [
                'method' => $method,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => false,
            ],
        ];

        $content = $request->getContent();
        if ($content !== null) {
            $contextOptions['http']['content'] = $content;
        }
        $headers = $request->composeHeaderLines($request);
        $contextOptions['http']['header'] = $headers;

        $contextOptions = ArrayHelper::merge($contextOptions, $this->composeContextOptions($request->getOptions()));

        $context = stream_context_create($contextOptions);
        $stream = fopen($url, 'rb', false, $context);
        $responseContent = stream_get_contents($stream);
        $metaData = stream_get_meta_data($stream);
        fclose($stream);

        $responseHeaders = isset($metaData['wrapper_data']) ? $metaData['wrapper_data'] : [];

        return $request->client->createResponse($responseContent, $responseHeaders);
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
            $section = 'http';
            if (strpos($key, 'ssl') === 0) {
                $section = 'ssl';
                $key = substr($key, 3);
            }
            Inflector::underscore($key);
            $contextOptions[$section][$key] = $value;
        }
        return $contextOptions;
    }
}