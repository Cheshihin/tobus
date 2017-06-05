<?php

namespace app\components;

use Yii;


/**
 * Вспомогательные функции
 */
class Helper
{
    /*
     * Функция возвращает дату в формате: чт, 20 апреля, 19:00 или формат: чт, 20 апреля 2017 года
     *
     * @return string
     */
    public static function getMainDate($unixtime, $type = 1)
    {
        $arWeekDays = ['пн', 'вт', 'ср', 'чт', 'пт', 'сб', 'вс'];
        $awMonths = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа',
            'сентября', 'октября', 'ноября', 'декабря'];

        if($type == 1) {
            return $arWeekDays[date('N', $unixtime) - 1]
            . date(', d ', $unixtime)
            . $awMonths[date('n', $unixtime) - 1]
            . date(', H:i', $unixtime);
        }elseif($type == 2) {

            $day_name = '';
            if(date('d.m.Y', $unixtime) == date('d.m.Y')) {
                $day_name = 'сегодня - ';
            }elseif(date('d.m.Y', $unixtime) == date('d.m.Y', time() + 86400)) {
                $day_name = 'завтра - ';
            }elseif(date('d.m.Y', $unixtime) == date('d.m.Y', time() - 86400)) {
                $day_name = 'вчера - ';
            }

            return $day_name
            . $arWeekDays[date('N', $unixtime) - 1]
            . date(', d ', $unixtime)
            . $awMonths[date('n', $unixtime) - 1]
            . date(' Y', $unixtime).' года';
        }
    }

    /*
     * Функция возвращает код выбранного дня
     */
    public static function getDayCode($date = '') {
        if($date == date('d.m.Y')) {
            return 'today';
        }elseif($date == date('d.m.Y', (time() + 86400))) {
            return 'tomorrow';
        }else {
            return 'another-day';
        }
    }

    /*
     * Функция преобразует код дня в unixtime
     */
    public static function getUnixtimeByDateCode($date_code)
    {
        if($date_code == 'today') {
            return time();
        }elseif($date_code == 'tomorrow') {
            return time() + 86400;
        }else {
            return '';
        }
    }

    /*
     * Функция возвращает имя класса в зависимости от кода даты
     *
     * @return string
     */
    public static function getClassByDayCode($day_code)
    {
        $aDayClass = [
            'today' => 'orange-day',
            'tomorrow' => 'purple-day',
            'another-day' => 'blue-day'
        ];
        return $aDayClass[$day_code];
    }

    /*
     * Функция возвращает заголовки для окна записи заказа (клиента)
     */
    public static function getOrderCreateTitle($day_code)
    {
        if($day_code == 'today') {
            return 'Запись заказа на сегодня '.Helper::getMainDate(time());
        }elseif($day_code == 'tomorrow') {
            return 'Запись заказа на завтра '.Helper::getMainDate(time() + 86400);
        }elseif($day_code == 'another-day') {
            return 'Запись заказа на другой день';
        }else {
            return '';
        }
    }

    /*
     * Функция возвращает имя класса в зависимости от установленной даты
     *
     * @return string
     */
    public static function getMainClass($date = '')
    {
        if(empty($date)) {
            $date = date('d.m.Y');
        }
        $day_code = self::getDayCode($date);

        return Helper::getClassByDayCode($day_code);
    }
}
