<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AdminAsset;
use app\assets\FontAwesomeAsset;
use yii\bootstrap\Modal;
use app\components\Helper;

AdminAsset::register($this);
FontAwesomeAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <?php $this->head() ?>
</head>
<body class="hold-transition skin-blue fixed sidebar-mini">
<?php /* skin-black fixed sidebar-mini  pace-done */ ?>
<?php $this->beginBody() ?>


<?php
/*
?>
<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'На главную',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
            'style' => 'background-color: #000099;'
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            Yii::$app->user->isGuest ? (
            ['label' => 'Login', 'url' => ['/site/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            )
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?php
        echo Breadcrumbs::widget([
            'homeLink' => ['label' => 'Главная', 'url' => '/'],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]);
        ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
<?php
// Модальное окно для загрузки содержимого с помощью ajax
Modal::begin([
    'header' => '<h4 class="modal-title">Заполните форму</h4>',
    'id' => 'default-modal',
    'size' => 'modal-md',
]);

*/
?>
<div class="wrap">
    <!-- Main Header -->
    <header class="main-header">

        <!-- Logo -->
        <a href="/admin" class="logo">
            <span class="logo-lg"><b>Администратор</b></span>
        </a>

        <!-- Header Navbar -->
        <?php /*
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- Messages: style can be found in dropdown.less-->
                    <li class="dropdown messages-menu">
                        <!-- Menu toggle button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-envelope-o"></i>
                            <span class="label label-success">4</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">You have 4 messages</li>
                            <li>
                                <!-- inner menu: contains the messages -->
                                <ul class="menu">
                                    <li><!-- start message -->
                                        <a href="#">
                                            <div class="pull-left">
                                                <!-- User Image -->
                                                <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                                            </div>
                                            <!-- Message title and timestamp -->
                                            <h4>
                                                Support Team
                                                <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                            </h4>
                                            <!-- The message -->
                                            <p>Why not buy a new awesome theme?</p>
                                        </a>
                                    </li>
                                    <!-- end message -->
                                </ul>
                                <!-- /.menu -->
                            </li>
                            <li class="footer"><a href="#">See All Messages</a></li>
                        </ul>
                    </li>
                    <!-- /.messages-menu -->

                    <!-- Notifications Menu -->
                    <li class="dropdown notifications-menu">
                        <!-- Menu toggle button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bell-o"></i>
                            <span class="label label-warning">10</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">You have 10 notifications</li>
                            <li>
                                <!-- Inner Menu: contains the notifications -->
                                <ul class="menu">
                                    <li><!-- start notification -->
                                        <a href="#">
                                            <i class="fa fa-users text-aqua"></i> 5 new members joined today
                                        </a>
                                    </li>
                                    <!-- end notification -->
                                </ul>
                            </li>
                            <li class="footer"><a href="#">View all</a></li>
                        </ul>
                    </li>
                    <!-- Tasks Menu -->
                    <li class="dropdown tasks-menu">
                        <!-- Menu Toggle Button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-flag-o"></i>
                            <span class="label label-danger">9</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">You have 9 tasks</li>
                            <li>
                                <!-- Inner menu: contains the tasks -->
                                <ul class="menu">
                                    <li><!-- Task item -->
                                        <a href="#">
                                            <!-- Task title and progress text -->
                                            <h3>
                                                Design some buttons
                                                <small class="pull-right">20%</small>
                                            </h3>
                                            <!-- The progress bar -->
                                            <div class="progress xs">
                                                <!-- Change the css width attribute to simulate progress -->
                                                <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                    <span class="sr-only">20% Complete</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <!-- end task item -->
                                </ul>
                            </li>
                            <li class="footer">
                                <a href="#">View all tasks</a>
                            </li>
                        </ul>
                    </li>
                    <!-- User Account Menu -->
                    <li class="dropdown user user-menu">
                        <!-- Menu Toggle Button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <!-- The user image in the navbar-->
                            <img src="dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                            <!-- hidden-xs hides the username on small devices so only the image appears. -->
                            <span class="hidden-xs">Alexander Pierce</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- The user image in the menu -->
                            <li class="user-header">
                                <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">

                                <p>
                                    Alexander Pierce - Web Developer
                                    <small>Member since Nov. 2012</small>
                                </p>
                            </li>
                            <!-- Menu Body -->
                            <li class="user-body">
                                <div class="row">
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Followers</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Sales</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Friends</a>
                                    </div>
                                </div>
                                <!-- /.row -->
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="#" class="btn btn-default btn-flat">Profile</a>
                                </div>
                                <div class="pull-right">
                                    <a href="#" class="btn btn-default btn-flat">Sign out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <!-- Control Sidebar Toggle Button -->
                    <li>
                        <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                    </li>
                </ul>
            </div>
        </nav>
        */ ?>

        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li><a href="/site/logout">Выход (<?= Yii::$app->user->identity->username ?>) </a> </li>
                    <li><a href="/" style="font-size: 24px; font-weight: 700;">ТОБУС</a></li>
                    <li style="padding: 7px 0 7px 15px; font-size: 12px; text-align: left; width: 160px; color: #FFFFFF;">
                        Текущая дата:<br />
                        <span id="system-time"><?= Helper::getMainDate(time(), 1); ?></span>
                    </li>
                </ul>
            </div>
        </nav>

        <?php
        /*NavBar::begin([
            'brandLabel' => ' <span class="sr-only">Toggle navigation</span> В диспетчерскую',
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                //'class' => 'navbar-inverse navbar-fixed-top'
                'class' => 'navbar navbar-static-top'
            ],
        ]);
        ?>
        <?php
        echo Nav::widget([
            'options' => [
                //'class' => 'navbar-nav navbar-right'
                'class' => 'navbar-custom-menu'
            ],
            'items' => [
                Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/site/login']]
                ) : (
                    '<li>'
                    . Html::beginForm(['/site/logout'], 'post')
                    . Html::submitButton(
                        'Logout (' . Yii::$app->user->identity->username . ')',
                        ['class' => 'btn btn-link logout']
                    )
                    . Html::endForm()
                    . '</li>'
                )
            ],
        ]);
        NavBar::end();*/
        ?>

    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">

        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">

            <?php
            $current_module = Yii::$app->controller->module->id;
            $current_controller = Yii::$app->controller->id;
            $current_route = $this->context->route;

//            echo "current_module=$current_module <br />";
//            echo "current_controller=$current_controller <br />";
//            echo "current_route=$current_route <br />";
            ?>

            <!-- Sidebar Menu -->
            <ul class="sidebar-menu">
                <li class="treeview <?= ($current_module == 'admin' && in_array($current_controller, ['city', 'direction', 'tariff'])) ? 'active' : '' ?>">
                    <a href="#">
                        <i class="fa fa-exchange"></i> <span>Маршруты и тарифы</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li<?= $current_controller == 'city' ? ' class="active"' : '' ?>>
                            <?= Html::a('<i class="glyphicon glyphicon-map-marker"></i> <span>Города</span>', '/admin/city'); ?>
                        </li>
                        <li<?= $current_controller == 'direction' ? ' class="active"' : '' ?>>
                            <?= Html::a('<i class="glyphicon glyphicon-road"></i> <span>Направления</span>', '/admin/direction'); ?>
                        </li>
                        <li<?= $current_controller == 'tariff' ? ' class="active"' : '' ?>>
                            <?= Html::a('<i class="glyphicon glyphicon-piggy-bank"></i> <span>Тарифы</span>', '/admin/tariff'); ?>
                        </li>
                    </ul>
                </li>

                <li class="treeview <?= ($current_module == 'admin' && in_array($current_controller, ['transport', 'driver', 'driver-accounting'])) ? 'active' : '' ?>">
                    <a href="#">
                        <i class="fa fa-truck"></i> <span>Транспорт</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li<?= $current_controller == 'transport' ? ' class="active"' : '' ?>>
                            <?= Html::a('<i class="fa fa-bus"></i> <span>Машины</span>', '/admin/transport'); ?>
                        </li>
                        <li<?= $current_controller == 'driver' ? ' class="active"' : '' ?>>
                            <?= Html::a('<i class="fa fa-wheelchair-alt"></i> <span>Водители</span>', '/admin/driver'); ?>
                        </li>
                        <li<?= $current_controller == 'driver-accounting' ? ' class="active"' : '' ?>>
                            <?= Html::a('<i class="fa fa-list-alt"></i> <span>Учет работы водителя</span>', '/admin/driver-accounting'); ?>
                        </li>
                    </ul>
                </li>

                <li class="treeview <?= ($current_module == 'admin' && in_array($current_controller, ['dispatcher', 'dispatcher-accounting'])) ? 'active' : '' ?>">
                    <a href="#">
                        <i class="glyphicon glyphicon-headphones"></i> <span>Диспетчерская</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li<?= $current_controller == 'dispatcher' ? ' class="active"' : '' ?>>
                            <?= Html::a('<i class="fa fa-reddit-alien"></i> <span>Диспетчера</span>', '/admin/dispatcher'); ?>
                        </li>
                        <li<?= $current_controller == 'dispatcher-accounting' ? ' class="active"' : '' ?>>
                            <?= Html::a('<i class="fa fa-fax"></i> <span>Учет работы диспетчера</span>', '/admin/dispatcher-accounting'); ?>
                        </li>
                    </ul>
                </li>

                <li class="treeview <?= ($current_module == 'admin' && in_array($current_controller, ['client', 'order'])) ? 'active' : '' ?>">
                    <a href="#">
                        <i class="fa fa-group"></i> <span>Пассажиры</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li<?= $current_controller == 'client' ? ' class="active"' : '' ?>>
                            <?= Html::a('<i class="fa fa-meh-o"></i> <span>Клиенты</span>', '/admin/client'); ?>
                        </li>
                        <li<?= $current_controller == 'order' ? ' class="active"' : '' ?>>
                            <?= Html::a('<i class="fa fa-tasks"></i> <span>Заказы</span>', '/admin/order'); ?>
                        </li>
                    </ul>
                </li>

                <li<?= ($current_module == 'admin' && $current_controller == 'informer-office' ? ' class="active"' : '') ?>>
                    <?= Html::a('<i class="fa fa-volume-control-phone"></i> <span>Диспетчерские</span>', '/admin/informer-office'); ?>
                </li>
                <li<?= ($current_module == 'admin' && $current_controller == 'user' ? ' class="active"' : '') ?>>
                    <?= Html::a('<i class="glyphicon glyphicon-user"></i> <span>Пользователи</span>', '/admin/user'); ?>
                </li>

            </ul>
            <!-- /.sidebar-menu -->
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <?php //if(in_array($current_controller, ['city', 'client'])) { ?>

            <?php if ($this->title) {?>
                <section class="content-header">
                    <h1 class="text-muted"><?= $this->title ?></h1>
                </section>
                <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    'homeLink' => ['label' => 'Администратор', 'url' => '/admin/'],
                    'encodeLabels' => false,
                    'options' => ['class' => 'breadcrumb breadcrumb-tobus'] // breadcrumb-lte
                ]); ?>
            <?php } ?>

        <?php /*}
        elseif($current_controller == 'direction') { ?>
            <section class="content-header">
                <h1 class="text-muted"><?= $this->title ?></h1>
                <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    'homeLink' => ['label' => 'Администратор', 'url' => '/admin/'],
                    'encodeLabels' => false,
                    'options' => ['class' => 'breadcrumb breadcrumb-lte'] // breadcrumb-lte
                ]); ?>
            </section>
        <?php }else { ?>


        <?php }*/ ?>

        <!-- Main content -->
        <section class="content">

            <!-- Your Page Content Here -->
            <?= $content ?>

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

</div>

<?php $this->endBody() ?>

<?php
// Модальное окно для загрузки содержимого с помощью ajax
Modal::begin([
    'header' => '<h4 class="modal-title">Заполните форму</h4>',
    'id' => 'default-modal',
    'size' => 'modal-md',
]);
?>
<div id='modal-content'>Загружаю...</div>
<?php Modal::end(); ?>

</body>
</html>
<?php $this->endPage() ?>
