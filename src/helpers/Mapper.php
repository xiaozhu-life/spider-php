<?php
/**
 * Created by PhpStorm.
 * User: Li Yulong
 * Date: 2015/11/24
 * Time: 14:43
 */

namespace xiaozhu\spider\helpers;

/**
 *
 *
 * 还没有测试不推荐使用
 *
 *
 * 用来将一个数组的值赋给一个对象 ， 或者将一个对象的属性赋给另一个对象， 数组键值或者属性名作为映射关系
 * Class Mapper
 * @package xiaozhu\spider\helpers
 * Author Liyulong
 */
class Mapper
{
    use CmdTrait;
    private $_isOverride = false;

    private $_filter = [];

    private $_handlers = [];

    /**
     * @param $filter array
     * @return $this
     */
    public function setFilter($filter)
    {
        $this->_filter = $filter;
        return $this;
    }

    /**
     * @param bool|true $isOverride
     * @return $this
     */
    public function override($isOverride = true)
    {
        $this->_isOverride = $isOverride;
        return $this;
    }

    /**
     * 字段名=>callable ,callable的参数为字段的值,返回处理后的字段值,
     * 签名为 function($value){ return $value};
     * @param $handlers array ["propertyName"=>callable,]
     * @return $this
     */
    public function setHandlers($handlers)
    {
        $this->_handlers = $handlers;
        return $this;
    }

    private $map = [];

    /**
     * 数据的映射关系
     * @param $map ["src-data-field"=>"dst-data-field",]
     * @return $this
     */
    public function setMap($map)
    {
        $this->map = $map;
        return $this;
    }

    public function run(&$dstObj,&$srcData)
    {
        self::map($dstObj,$srcData,$this->_isOverride,$this->_filter,$this->_handlers,$this->map);
        return $this;
    }

    /**
     * @param object $dstObj 目标对象
     * @param array|object $srcData 用来给对象赋值的数据 可以是数组或者对象
     * @param bool|false $isOverride 是否覆盖dst已有的属性值
     * @param array $filter 不赋值的属性
     * @param array $handlers 字段名=>callable ,callable的参数为字段的值,返回处理后的字段值,
     *                        签名为 function($srcValue,$src,$dst){ return $value};
     * @param array $map 字段的映射 ["src-data-field"=>"dst-data-field",]
     * @throws \Exception
     */
    public static function map(&$dstObj,&$srcData,$isOverride = false,$filter = [],$handlers = [],$map = [])
    {
        $isFilterExists = !empty($filter);
        $isArray = is_array($srcData);
        $isObj = is_object($srcData);
        if(!($isObj || $isArray))
        {
            throw new \Exception("srcData need be array or obj:".var_export($srcData));
        }
        $revMap = array_flip($map);
        foreach($dstObj as $name=>$value)
        {
            if($isFilterExists && in_array($name,$filter))
            {
                continue;
            }
            $srcName = $name;
            if(!empty($revMap) && isset($revMap[$name]))
            {
                $srcName = $revMap[$name];
            }
            if(!empty($dstObj->$name))
            {
                if($isOverride == false)
                {
                    continue;
                }
            }
            if( ($isArray && isset($srcData[$srcName])) ||
                ($isObj   && isset($srcData->$srcName)) )
            {
                $value = $isArray ? $srcData[$srcName] : $srcData->$srcName;
            }

            if(isset($handlers[$name]) && !empty($srcData))
            {
                $value = $handlers[$name](empty($value) ? null : $value ,$srcData,$dstObj);
            }
            if(empty($value))
            {
                continue;
            }
            $dstObj->$name = $value;
        }
    }

    /**
     * @param object $dstObj 目标对象
     * @param object|array $srcData 用来给对象赋值的数据 可以是数组或者对象
     * @param $fields ["src-data-field"=>"dst-data-field",] 或者 ["dst-data-field","dst-data-field"]
     * @param array $handlers 字段名=>callable ,callable的参数为字段的值,返回处理后的字段值,
     *                        签名为 function($value){ return $value};
     * @throws \Exception
     */
    public static function mapWithFields(&$dstObj,&$srcData,$fields,$handlers = [])
    {
        $isArray = is_array($srcData);
        $isObj = is_object($srcData);
        if(!($isObj || $isArray))
        {
            throw new \Exception("srcData need be array or obj:".var_export($srcData));
        }
        $srcFields = array_flip($fields);
        foreach($dstObj as $name=>$value)
        {
            if(!in_array($name,$fields))
            {
                continue;
            }
            $srcFieldName = $name;
            if(isset($srcFields[$name]))
            {
                $maySrcFieldName = $srcFields[$name];
                if(!is_int($maySrcFieldName))
                {
                    $srcFieldName = $maySrcFieldName;
                }
            }
            if( ($isArray && isset($srcData[$srcFieldName])) ||
                ($isObj   && isset($srcData->$srcFieldName)) )
            {
                $value = $isArray ? $srcData[$srcFieldName] : $srcData->$srcFieldName;
            }

            if(isset($handlers[$name]) && !empty($srcData))
            {
                $value = $handlers[$name](empty($value) ? null : $value ,$srcData,$dstObj);
            }
            if(empty($value))
            {
                continue;
            }
            $dstObj->$name = $value;
        }
    }
}