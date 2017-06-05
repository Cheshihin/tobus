<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\User;
use yii\helpers\ArrayHelper;

$this->title = 'Авторизация';
?>


<div class="container-fluid">
    <div class="form-signin">

        <?php $form = ActiveForm::begin(); ?>

            <h2 class="form-signin-heading"><?= Html::encode($this->title) ?></h2>

            <p style="color: #a94442;"><?= Yii::$app->session->getFlash('error') ?></p>

            <?= $form->field($model, 'rememberMe')->label(false)->hiddenInput() ?>

            <?= $form->field($model, 'username')
                ->dropDownList(
                    ArrayHelper::map(User::find()->all(), 'username', 'username'),
                    ['class' => 'input-block-level']
                ) ?>

            <?= $form->field($model, 'password')->passwordInput(['class'=>'input-block-level']) ?>

            <div class="center">
                 <?= Html::submitButton('Войти', ['class' => 'btn', 'name' => 'login-button',]) ?>
            </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
