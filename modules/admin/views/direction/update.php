<?php

use yii\helpers\Html;


$this->title = 'Редактирование направления: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Направления', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="direction-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
