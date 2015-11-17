<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 31.07.2015
 * Time: 18:46
 *
 * CFF - Class Fast Function
 * Поплуярные функции
 */

namespace common\helpers;

class CFF
{
    /**
     * Преобразование даны в формат YYYY-mm-dd или dd.mm.YYYY
     *
     * @param $date - входная дата в одном из 2-х форматов
     * @param bool|false $showtime - показывать/ не показывать время на выходе?
     */
    public static function formatData($date, $showtime=false){
        $chapter = explode(' ',$date);
        // Дата в формате dd.mm.YYYY
        if (preg_match("/(0[1-9]|[12][0-9]|3[01])\.(0[1-9]|1[012])\.(19|20)\d\d/",$chapter[0])) {
            $m = explode('.',$chapter[0]);
            $date = $m[2].'-'.$m[1].'-'.$m[0];
        }
        // Дата в формате YYYY-mm-dd
        elseif (preg_match("/[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])/",$chapter[0])) {
            $m = explode('-',$chapter[0]);
            $date = $m[2].'.'.$m[1].'.'.$m[0];
        }
        // Добавить время к дате
        if ($showtime && isset($chapter[1]))
            $date.=' '.$chapter[1];
        return $date;
    }

    /**
     * Получение первой части алиаса (до первого /)
     *
     * @param string $url - входная адресная строка
     */
    public static function getAlias($url){
        return explode('/', $url);
    }

    public static function getThumb($name) {
        $ext = "." . end(explode(".", $name));
        $thumb = substr($name, 0, -strlen($ext)) . '_thumb' . $ext;
        return $thumb;

    }

    //Удаление директории с файлами
    public static function removeDir($path)
    {
        if (file_exists($path) && is_dir($path)) {
            $dirHandle = opendir($path);
            while (false !== ($file = readdir($dirHandle)))
            {
                if ($file != '.' && $file != '..') // исключаем папки с назварием '.' и '..'
                {
                    $tmpPath = $path . '/' . $file;
                    chmod($tmpPath, 0777);
                    if (is_dir($tmpPath))
                        CFF::RemoveDir($tmpPath);
                    else
                        if (file_exists($tmpPath))
                            unlink($tmpPath);
                }
            }
            closedir($dirHandle);
            // удаляем текущую папку
            if (file_exists($path)) {
                rmdir($path);
            }
        }
    }

    //Сокращение текста до необходимого количества символов
    public static function shortString($string, $count_symbol)
    {
        $count_string = strlen($string);
        if ($count_string <= $count_symbol)
            $string = strip_tags($string);
        else
            $string = strip_tags(substr($string, 0, $count_symbol)) . "...";
        return $string;
    }

    //Получение IP адерса пользователя
    public static function getIP(){
        if (!empty($_SERVER['HTTP_CLIENT_IP'])){
            //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

}