<?php

use yii\helpers\Html;

$this->title = 'Редактирование города &laquo;' . $model->name . '&raquo;';
$this->params['breadcrumbs'][] = ['label' => 'Города', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="city-update">
    <?= $this->render('_form', [
        'model' => $model,
        'pointSearchModel' => $pointSearchModel,
        'pointDataProvider' => $pointDataProvider,
    ]) ?>
</div>
