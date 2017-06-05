<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TripStatic */

$this->title = 'Create Trip Static';
$this->params['breadcrumbs'][] = ['label' => 'Trip Statics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trip-static-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
