<?php
/**
 * Created by PhpStorm.
 * User: Li Yulong
 * Date: 2015/11/12
 * Time: 17:43
 */

namespace xiaozhu\spider\helpers;


trait CmdTrait
{
    public static function i($str, $depth = 1, $enter = true){
        $br = PHP_SAPI == "cli" ? "\n" : "<br/>";
        $str = is_array($str) ? implode($br, $str) : $str;
        echo str_pad("",strlen("\t")*($depth-1),"\t").$str;
        echo $enter ? $br : "";
    }
}