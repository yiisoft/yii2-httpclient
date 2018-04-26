<?php
/* @var $panel yii\httpclient\debug\HttpClientPanel */
/* @var $queryCount int */
/* @var $queryTime int */
?>
<?php if ($queryCount): ?>
<div class="yii-debug-toolbar-block">
    <a href="<?= $panel->getUrl() ?>" title="Executed <?= $queryCount ?> HTTP Requests which took <?= $queryTime ?>.">
        HTTP Requests <span class="label label-info"><?= $queryCount ?></span> <span class="label"><?= $queryTime ?></span>
    </a>
</div>
<?php endif; ?>
