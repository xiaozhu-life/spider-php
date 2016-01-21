<?php
/**
 * Created by PhpStorm.
 * User: Li Yulong
 * Date: 2015/11/13
 * Time: 14:58
 */

namespace xiaozhu\spider\helpers;


class TimeHelper
{

    /**
     * @param $dayOffset int -1 当前的前一天 , 1 当前后一天
     * @return int 获取今天凌晨0点的timestamp
     */
    public static function startTimeOfDay($dayOffset = 0)
    {
        $time = ($offset = intval($dayOffset)) ? strtotime($offset." days") : time();
        return strtotime(date("Y-m-d", $time));
    }

    /**
     * @param $time int|string
     * @return int
     */
    public static function startUnixTimeOfDay($time)
    {
        if(is_int($time))
        {
            if($time > 2147483647)
            {
                $time = $time/1000;
            }
            return strtotime(date("Y-m-d",$time));
        }
        if(is_string($time))
        {
            return self::startUnixTimeOfDay(strtotime($time));
        }
        return false;
    }
    /**
     * @param $time int|string
     * @return int
     */
    public static function endUnixTimeOfDay($time)
    {
        $time = self::startUnixTimeOfDay($time);
        if($time !== false)
        {
            return $time + 3600 * 24;
        }
        return false;
    }

    /**
     * @return int|mixed 获取毫秒的unix时间戳
     */
    public static function getMicroTimeAsFloat()
    {
        if(function_exists("microtime"))
        {
            $timeStr = "". microtime("get_as_float") * 1000;
            if(strpos($timeStr,".") > -1)
            {
                $timeStr = explode(".",$timeStr)[0];
            }
            return floatval($timeStr);
        }
        else
        {
            return floatval(time()) * 1000;
        }
    }

    /**
     * 默认返回当前年份
     * @param int $yearOffset 与当前年份的距离
     * @return int
     */
    public static function getYear($yearOffset = 0)
    {
        $curYear = date('Y');
        return (int)$curYear + $yearOffset;
    }

    /**
     * @param $t int unix时间戳
     * @return string 返回 xx分钟/小时/天前
     */
    public static function humanDate($t)
    {
        $time = time();
        $x = $time - $t;
        if ($x <= 0) $x = 1;
        if ($x < 60) {
            $r = $x . '秒前';
        } elseif ($x < 3600) {
            $r = intval($x / 60) . '分钟前';
        } elseif ($x < 3600 * 24) {
            $r = intval($x / 3600) . '小时前';
        } elseif ($x < 3600 * 24 * 30) {
            $r = intval($x / (3600 * 24)) . '天前';
        } elseif ($x < 3600 * 24 * 30 * 12) {
            $r = intval($x / (3600 * 24 * 30)) . '个月前';
        } else {
            $r = intval($x / (3600 * 24 * 30 * 12)) . '年前';
        }
        return $r;
    }
}