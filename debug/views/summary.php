<?php
/* @var $panel yii\httpclient\debug\HttpClientPanel */
/* @var $queryCount integer */
/* @var $queryTime integer */
?>
<?php if ($queryCount): ?>
<div class="yii-debug-toolbar__block">
    <a href="<?= $panel->getUrl() ?>" title="Executed <?= $queryCount ?> HTTP Requests which took <?= $queryTime ?>.">
        HTTP Requests <span class="yii-debug-toolbar__label yii-debug-toolbar__label_info"><?= $queryCount ?></span> <span class="yii-debug-toolbar__label"><?= $queryTime ?></span>
    </a>
</div>
<?php endif; ?>
