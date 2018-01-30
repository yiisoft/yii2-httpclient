<?php

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

Yii::setAlias('@yiiunit/extensions/httpclient', __DIR__);
Yii::setAlias('@yii/httpclient', dirname(__DIR__));

require_once(__DIR__ . '/compatibility/phpunit_constraint.php');
require_once(__DIR__ . '/compatibility/phpunit_testcase.php');
