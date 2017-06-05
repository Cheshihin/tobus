<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use kartik\date\DatePicker;
use app\components\Helper;
use yii\bootstrap\Modal;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap <?= Helper::getMainClass(Yii::$app->request->get('date')) ?>">

    <?= $this->render('top-menu'); ?>

    <div class="container" style="padding-left: 0; padding-right: 0; padding-top: 20px;">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<?php $this->endBody() ?>

<div id="order-create-modal" class="fade modal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-md" style="width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <span class="modal-title">Запись заказа</span>
            </div>
            <div class="modal-body">
                <div id="modal-content">Загружаю...</div>

            </div>
        </div>
    </div>
</div>

<?php
// Модальное окно для загрузки содержимого с помощью ajax
/*
Modal::begin([
    'header' => '<h4 class="modal-title">Заполните форму</h4>',
    'id' => 'default-modal',
    'size' => 'modal-md',
]);
?>
<div id='modal-content'>Загружаю...</div>
<?php Modal::end();
*/ ?>

</body>
</html>
<?php $this->endPage() ?>
