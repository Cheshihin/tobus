<?php
use yii\helpers\Html;


// Параметры/настройки для виджета в js
$js = '
    function SWConvertName(field_name) {
        return field_name.replace(\'[\',\'-\').replace(\']\',\'\').toLowerCase();
    }

    var field_name = SWConvertName("'.$name.'");
    if(typeof(sw_setting) == "undefined") {
        sw_setting = {};
    }
    sw_setting[field_name] = {
        ajax_url: "'.$ajax['url'].'",
        element_name: "'.$name.'",
        field_name: field_name,
        sw_is_open: false
    };
';
if(isset($ajax['data'])) {
    $js .= 'sw_setting[field_name].ajax_data = '.$ajax['data'].';';
}
if(isset($ajax['afterRequest'])) {
    $js .= 'sw_setting[field_name].afterRequest = '.$ajax['afterRequest'].';';
}
if(isset($ajax['afterSelect'])) {
    $js .= 'sw_setting[field_name].afterSelect = '.$ajax['afterSelect'].';';
}
if(!empty($open_url)) {
    $js .= 'sw_setting[field_name].open_url = '.$open_url;
}
if(!empty($add_new_value_url)) {
    $js .= 'sw_setting[field_name].add_new_value_url = '.$add_new_value_url;
}

$this->registerJs($js, \yii\web\View::POS_END);
?>



<div class="form-control sw-element" attribute-name="<?= $name ?>" <?= (isset($options['disabled']) ? 'disabled="'.$options['disabled'].'" ' : '') ?>>
    <span class="sw-text">
        <span class="sw-delete glyphicon glyphicon-remove" <?= (empty($value) ? 'style="display: none;"' : '') ?>></span>
        <?php if(!empty($open_url)) { ?>
            <span class="sw-open glyphicon glyphicon-eye-open" <?= (empty($value) ? 'style="display: none;"' : '') ?>></span>
        <?php } ?>
        <span class="sw-value">
            <?php
            if(!empty($initValueText)) {
                echo $initValueText;
            }else {
                if(!empty($value)) {
                    echo $value;
                }else {
                    if(isset($options['placeholder'])) {
                        echo '<span class="sw-placeholder">' . $options['placeholder'] . '</span>';
                    }
                }
            }
            ?>
        </span>
    </span>
    <span class="select2-selection__arrow" role="presentation">
        <b></b>
    </span>
    <?= Html::hiddenInput($name, $value, $options) ?>
</div>
<div class="sw-outer-block" style="display: none;">
    <div class="sw-inner-block">
        <input type="text" class="sw-search form-control">
        <div class="sw-select-block" style="display: none;">
            <!-- сюда можно засунуть preload-рисунок или что-то подобное -->
        </div>
    </div>
</div>