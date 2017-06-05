<?php

use yii\helpers\Html;


$this->title = 'Добавление города';
$this->params['breadcrumbs'][] = ['label' => 'Города', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="city-create">
    <?= $this->render('_form', [
        'model' => $model,
        'pointSearchModel' => $pointSearchModel,
        'pointDataProvider' => $pointDataProvider,
    ]) ?>
</div>
