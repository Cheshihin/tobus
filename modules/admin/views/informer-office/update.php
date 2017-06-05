<?php

use yii\helpers\Html;

$this->title = 'Редактирование диспетчерской &laquo;' . $model->name . '&raquo;';
$this->params['breadcrumbs'][] = ['label' => 'Диспетчерские', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="informer-office-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
