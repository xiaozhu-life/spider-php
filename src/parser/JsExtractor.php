<?php
/**
 * Created by PhpStorm.
 * User: Li Yulong
 * Date: 2015/12/17
 * Time: 16:43
 */

namespace xiaozhu\spider\parser;


use xiaozhu\spider\helpers\StringChars;
use yii\validators\SafeValidator;

class JsExtractor
{
    /**
     * 只能找到变量的第一个值
     * @param $varName string
     * @param $str string
     * @return string
     */
    public static function extractVar($str,$varName)
    {
        $strChars = new StringChars($str);
        $pos = $strChars->skipToNextMatch($varName);
        if($pos === false)
        {
            return null;
        }
        $start = ["{","[",'"',"'"];
        $end   = ["}","]",'"'."'"];
        $numberChars = ['-','0','1','2','3','4','5','6','7','8','9'];
        $start_end = [
            "{"=>"}",
            "["=>"]",
            '"'=>'"',
            "'"=>"'",
        ];
        $end_start = [
            "}"=>"{",
            "]"=>"[",
            '"'=>'"',
            "'"=>"'",
        ];
        $eqs = ["'",'"'];
        $close = [
            ';'
        ];

        while(($pos = $strChars->skipToNextMatch($varName)) !== false)
        {
            $valueStart = false;
            //找到 "=" 赋值符号
            $res = self::skipWriteSpaceBeforChars($strChars,['=']);
            if(!$res)
            {
                continue;
            }
            $strChars->next();
            $res = self::skipWriteSpaceBeforChars($strChars,$start);
            if(!$res)
            {
                continue;
            }
            $startChar = $strChars->current();
            $endChar = $start_end[$startChar];
            $stack = [];
            $res = '';
            $allSymbols = array_merge($start,$end);
            $invalid = false;
            $isSlash = false;
            while($strChars->valid())
            {
                $c = $strChars->current();
                if($isSlash)
                {
                    $isSlashed = true;
                }
                $isSlash = $c == '\\';
                $res = $res.$c;
                if(in_array($startChar,$eqs))
                {
                }
            }

        }

        return '';
    }

    /**
     * @param $stringChars StringChars
     * @param array $stopChars
     * @return bool
     */
    private static function skipWriteSpaceBeforChars($stringChars,$stopChars = ['='])
    {
        while($stringChars->valid())
        {
            $c = $stringChars->current();
            if(in_array($c,[' ',"\n","\t"]))
            {
                $stringChars->next();
            }
            elseif(in_array($c,$stopChars))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        return false;
    }

}