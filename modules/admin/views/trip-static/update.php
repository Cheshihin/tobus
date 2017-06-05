<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TripStatic */

$this->title = 'Update Trip Static: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Trip Statics', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trip-static-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
