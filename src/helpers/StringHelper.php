<?php
/**
 * Created by PhpStorm.
 * User: Li Yulong
 * Date: 2015/11/9
 * Time: 10:30
 */

namespace xiaozhu\spider\helpers;


trait StringHelper
{
    public static function removeBom($contents){
        $charset[1] = substr($contents, 0, 1);
        $charset[2] = substr($contents, 1, 1);
        $charset[3] = substr($contents, 2, 1);
        if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
            $contents = substr($contents, 3);
        }
        return $contents;
    }

    /**
     * @param $str
     * @param $chars array 分割字符串的 字符 表
     * @return array
     */
    public static function sliceStrByChar($str,$chars)
    {
        $res = [];
        $item = '';
        $str = self::mbStringToArray($str);
        foreach($str as $c)
        {
            if(in_array($c,$chars))
            {
                $res[] = $item;
                $item = '';
                continue;
            }
            $item = $item.$c;
        }
        if(!empty($item))
        {
            $res[] = $item;
        }
        return $res;
    }

    /**
     * @param $string
     * @return array
     */
    public static function mbStringToArray ($string,$encode = "UTF-8") {
        $len = mb_strlen($string);
        $res = [];
        while ($len) {
            $res[] = mb_substr($string,0,1,$encode);
            $string = mb_substr($string,1,$len,$encode);
            $len = mb_strlen($string);
        }
        return $res;
    }

    /**
     * @param $str string
     * @param $char string 返回$char之前的字符串
     * @return string
     */
    public static function getStrBefore($str,$char)
    {
        $str = trim($str);
        if(strpos($str,$char) !== false)
        {
            $strArr = explode($char,$str);
            return $strArr[0];
        }
        return $str;
    }

    /**
     * @param $str string
     * @param $char string
     * @return string 返回$char之后的字符串
     */
    public static function getStrAfter($str,$char)
    {
        $str = trim($str);
        $pos = mb_strpos($str,$char);
        if($pos === false)
        {
            return $str;
        }
        else
        {
            return mb_substr($str,$pos + mb_strlen($char),mb_strlen($str) - $pos);
        }
    }

    /**
     * 去掉首尾包括全角在内的空格
     */
    public static function trimAll($str)
    {
        return trim($str);
    }
    /**
     * 找出第一个 符合 $start .... $end 的子串
     * @param $str string 需要被处理的html字符串
     * @param $start string 需要得到的 子串 的开始的标志
     * @param $end string 需要得到的 子串 的结束的标志
     * @return string 返回 被 $start $end 包围的子串
     */
    public static function findFirstMatchedSubStr($str,$start,$end){

        $pos   = strpos($str,$start);
        if($pos === false){
            return "";
        }
        $str  = substr($str,$pos);
        $pos   = strpos($str,$end);
        if($pos === false){
            return "";
        }
        $str  = substr($str,0,$pos+strlen($end));

        return $str;
    }


    /**
     * 正则表达式获取第一个match的字符串
     * @param $pattern
     * @param $str
     * @return string
     */
    public static function getMatchStr($pattern,$str)
    {
        if(preg_match($pattern, $str, $m)){
            return $m[1];
        }else{
            return false;
        }
    }

    /**
     * @param $chineseNum string 一二三
     * @return bool|int
     */
    public static function getNumberFromChineseNum($chineseNum)
    {
        $allNum = "一二三四五六七八九十";
        $sc = new StringChars($allNum);
        $res = 1;
        foreach($sc as $c)
        {
            if($c == $chineseNum)
            {
                return $res;
            }
            $res ++;
        }
        return false;
    }

}