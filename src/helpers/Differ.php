<?php
/**
 * Created by PhpStorm.
 * User: Li Yulong
 * Date: 2016/1/6
 * Time: 15:42
 */

namespace xiaozhu\spider\helpers;


class Differ
{
    /**
     * 判断两个数组或者对象 在某些字段上是否相等
     * @param $a object|array
     * @param $b object|array
     * @param $fields ["a-field-name"=>"b-field-name"] OR ["field-name"] 为空表示所有字段比较
     * @return bool
     */
    public static function isEqualWhen($a,$b,$fields = [])
    {
        if($a === $b)
        {
            return true;
        }
        if(!is_array($a) && !is_object($a) && !is_array($b) && !is_object($b))
        {
            return $a == $b;
        }
        $fieldsExists = !empty($fields);
        $aFields = array_flip($fields);
        $noThisField = function(){};
        foreach($b as $bFieldName => $bValue)
        {
            if($fieldsExists && !in_array($bFieldName,$fields))//检查当前字段是否在指定比较的字段里
            {
                continue;
            }
            $aFieldName = $fieldsExists ? $aFields[$bFieldName] : $bFieldName;//确定a中的字段名
            if(is_int($aFieldName))//不允许为数字
            {
                $aFieldName = $bFieldName;
            }
            $aValue = is_object($a) ? (isset($a->$aFieldName) ? $a->$aFieldName : $noThisField)
                                    : (isset($a[$aFieldName]) ? $a[$aFieldName] : $noThisField);//获取a在当前字段的值
            if($aValue === $noThisField)
            {
                return false;
            }
            if($aValue != $bValue)
            {
                return false;
            }
        }
        return true;
    }
}