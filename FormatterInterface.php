<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\httpclient;

/**
 * FormatterInterface represents HTTP message formatter.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
interface FormatterInterface
{
    /**
     * Formats given HTTP document.
     * @param MessageInterface $httpDocument HTTP document instance.
     * @return string formatted content.
     */
    public function format(MessageInterface $httpDocument);
} 