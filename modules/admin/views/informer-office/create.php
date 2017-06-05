<?php

use yii\helpers\Html;

$this->title = 'Добавление диспетчерской';
$this->params['breadcrumbs'][] = ['label' => 'Диспетчерские', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="informer-office-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
