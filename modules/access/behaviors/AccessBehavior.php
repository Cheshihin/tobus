<?php

namespace app\modules\access\behaviors;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\di\Instance;
use yii\base\Module;
use yii\web\User;
use yii\web\ForbiddenHttpException;

/**
 * Глобальное поведение проверки прав доступа.
 *
 * Class AccessBehavior
 * @package backend\modules\access\behaviors
 */
class AccessBehavior extends AttributeBehavior
{
    public $login_url = '/site/login';

    /**
     * @return array
     */
    public function events()
    {
        return [Module::EVENT_BEFORE_ACTION => 'interception'];
    }


    public function interception($event)
    {
        if(Yii::$app->user->isGuest && Yii::$app->request->url != $this->login_url) {
            Yii::$app->response->redirect($this->login_url)->send();
        }
    }
}
