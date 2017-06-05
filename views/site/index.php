<?php
use yii\helpers\Html;
use app\components\Helper;
use app\models\Direction;
use app\models\Trip;
use app\models\TripStatic;
use yii\helpers\Url;

$direction_list = Direction::find()->all();

//echo 'date='.date('d.m.Y H:i', 1468456200).'<br />'; // 25.04.2016  14.07.2016
$selected_unixdate = (!empty(Yii::$app->request->get('date')) ? strtotime(Yii::$app->request->get('date')) : time());
//echo 'selected_unixdate='.$selected_unixdate.'<br />';

$aDirections = [];
$has_trips = false;

$user = Yii::$app->user->identity;

/*
foreach($direction_list as $key => $direction) {
    $trips = Trip::find()
        ->where(['direction_id' => $direction->id])
        ->andWhere(['>=', 'date', $selected_unixdate])
        ->andWhere(['<', 'date', $selected_unixdate + 86400])
        ->all();
    $aDirections[] = [
        'direction' => $direction,
        'trips' => $trips
    ];

    if(count($trips) > 0) {
        $has_trips = true;
    }
}

if($has_trips == false) {
    //echo "рейсов на выбранную дату не существует - используем базовое расписание <br />";

    $trips = TripStatic::find()->where(['direction_id' => $direction->id])->all();

    $aDirections = [];
    foreach($direction_list as $key => $direction) {
        $aDirections[] = [
            'direction' => $direction,
            'trips' => $trips
        ];
    }

}*/

//$this->registerJsFile('js/site.js', ['depends' => 'app\assets\AdminAsset']); - уже подключен в шаблоне
?>
<div class="row">
    <!--<div class="col-tobus-left">&nbsp;</div>-->

    <div class="col-tobus-center">

        <?php
        //if ($has_trips == true) {
        foreach ($aDirections as $key => $aDirection)
        {
            $direction = $aDirection['direction'];
            ?>

            <div class="<?= (($key == count($direction_list) - 1) ? 'col-tobus-center-right' : 'col-tobus-center-left') ?>">

                <p class="sh_route"><?= $direction->sh_name ?></p>
                <table class="info-list <?= (($key == count($direction_list) - 1) ? 'info-list-right' : '') ?>">
                    <tbody>
                    <?php
                    foreach ($aDirection['trips'] as $trip) { ?>
                        <tr class="752">
                            <td rowspan="3" class="span1"></td>
                            <td class="span2 points"><?= $trip->start_time ?></td>
                            <td rowspan="3" class="reis_name span5">
                                <div class="reis_name_content">
                                    <a href="<?= Url::to(['trip/trip-orders', 'trip_id' => $trip->id]) ?>"><?= $trip->name ?></a>
                                    <span class="add_order_plus" trip-id="<?= $trip->id ?>"><i class="glyphicon glyphicon-plus-sign"></i></span>
                                </div>
                            </td>
                            <td rowspan="3" class="span2">
                                <span class="order_places many_orders" rel="752"><?= count($trip->orders) ?>
                                    /0</span>
                            </td>
                            <td rowspan="3" class="span2"></td>
                        </tr>
                        <tr class="752">
                            <td class="points"><?= $trip->mid_time ?></td>
                        </tr>
                        <tr class="752">
                            <td class="points"><?= $trip->end_time ?></td>
                        </tr>
                        <tr class="empty_tr 752"></tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>

        <?php
        }
        /*}else {

            foreach ($aDirections as $key => $aDirection)
            {
                $direction = $aDirection['direction'];
                ?>

                <div class="<?= (($key == count($direction_list) - 1) ? 'col-tobus-center-right' : 'col-tobus-center-left') ?>">

                    <p class="sh_route"><?= $direction->sh_name ?></p>
                    <table class="info-list <?= (($key == count($direction_list) - 1) ? 'info-list-right' : '') ?>">
                        <tbody>
                        <?php
                        foreach ($aDirection['trips'] as $trip) { ?>
                            <tr class="752">
                                <td rowspan="3" class="span1"></td>
                                <td class="span2 points"><?= $trip->start_time ?></td>
                                <td rowspan="3" class="reis_name span5">
                                    <div class="reis_name_content">
                                        <a href="#"><?= $trip->name ?></a>
                                        <span class="add_order_plus" trip-id="<?= $trip->id ?>"><i class="glyphicon glyphicon-plus-sign"></i></span>
                                    </div>
                                </td>
                                <td rowspan="3" class="span2">
                                        <span class="order_places many_orders" rel="752">0/0</span>
                                </td>
                                <td rowspan="3" class="span2"></td>
                            </tr>
                            <tr class="752">
                                <td class="points"><?= $trip->mid_time ?></td>
                            </tr>
                            <tr class="752">
                                <td class="points"><?= $trip->end_time ?></td>
                            </tr>
                            <tr class="empty_tr 752"></tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>

        <?php }
        }*/ ?>
    </div>

    <div class="col-tobus-right-1">&nbsp;</div>

    <div class="col-tobus-right-2-3">

        <div class="row-fluid disp_info">
            <span>Имя пользователя: <b><?= ($user != null ? $user->fullname : '');?></b></span> <a class="icon-remove-sign logout"></a><br/>
            <span class="user_role">Группа: <b><?= ($user != null && $user->userRole ? $user->userRole->name : ''); ?></b></span><br/>
            <span>Время входа: <b><?= ($user != null && $user->last_login_date > 0 ? date('Y.m.d H:i:s', ($user->last_login_date)) : '');?></b></span><br/>
            <hr/>
        </div>

        <?= Html::a('Запись на сегодня', ['#'], ['id' => 'new-order-today', 'class' => 'btn btn-default']); ?>

        <?= Html::a('Записать на завтра', ['#'], ['id' => 'new-order-tomorrow', 'class' => 'btn btn-default']); ?>

        <?= Html::a('Записать на другой день', ['#'], ['id' => 'new-order-another-day', 'class' => 'btn btn-default']); ?>

        <hr />

        <?= Html::a('Расстановка', ['#'], ['id' => 'edit-trip', 'class' => 'btn btn-default']); ?>

        <hr />

        <?= Html::a('Отчет отображаемого дня', ['#'], ['id' => 'day-report', 'class' => 'btn btn-default']); ?>

        <?= Html::a('Панель администратора', ['/admin/'], ['id' => 'admin-panel', 'class' => 'btn btn-default']); ?>

        <input id="client-search" type="text" value="" class="" placeholder="Поиск пассажира по номеру" />

    </div>

</div>
