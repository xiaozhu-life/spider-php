<?php
/**
 * Created by PhpStorm.
 * User: Li Yulong
 * Date: 2015/12/3
 * Time: 13:06
 */

namespace xiaozhu\spider\parser;


use xiaozhu\spider\parser\conf\ConfNode;
use xiaozhu\spider\parser\conf\ConfSymbols;
use xiaozhu\spider\helpers\CmdTrait;

class JsonParser
{
    use CmdTrait;
    /**
     * @param $conf ConfNode
     * @param $json string
     * @return array|float|int|String
     */
    public static function parseJson($conf,$json)
    {
        $data = json_decode($json);
        $result = self::queryValue($conf,$data);
        return $result;
    }

    /**
     * @param $conf ConfNode
     * @param $data object|array
     * @return object|array|string|int|float|double
     */
    private static function queryValue($conf, $data)
    {
        if(!empty($conf->selector))
        {
            $paths = explode(ConfSymbols::JsonPathSeperator, $conf->selector);
            foreach($paths as $p)
            {
                if(empty($data))
                {
                    return null;
                }
                if(is_array($data) && isset($data[$p]))
                {
                    $data = $data[$p];
                }
                elseif(is_object($data) && isset($data->$p))
                {
                    $data = $data->$p;
                }
                else
                {
                    $data = null;
                }
            }
        }
        switch($conf->type)
        {
            case ConfSymbols::TypeInt:
                return self::parseInt($data);
            case ConfSymbols::TypeFloat:
                return self::parseFloat($data);
            case ConfSymbols::TypeString:
                return self::parseString($data);
            case ConfSymbols::TypeBool:
                return self::parseBool($data);
            case ConfSymbols::TypeArray:
                return self::parseArray($data,$conf);
            case ConfSymbols::TypeObject:
                return self::parseObject($data,$conf);
            case ConfSymbols::TypeNull:
            default:
                break;
        }
        return null;
    }

    private static function parseInt($data)
    {
        if(is_int($data))
        {
            return $data;
        }
        elseif(is_string($data) || is_float($data)
            || is_double($data) || is_numeric($data)
            || is_bool($data))
        {
            return intval($data);
        }
        return null;
    }

    private static function parseFloat($data)
    {
        if(is_float($data))
        {
            return $data;
        }
        elseif(is_string($data) || is_integer($data)
            || is_double($data) || is_numeric($data)
            || is_bool($data))
        {
            return floatval($data);
        }
        return null;
    }

    private static function parseString($data)
    {
        if(is_string($data))
        {
            return $data;
        }
        elseif(is_numeric($data)||is_int($data)
            ||is_float($data)||is_double($data)
            ||is_bool($data))
        {
            return strval($data);
        }
        elseif(!empty($data))
        {
            return json_encode($data);
        }
        return null;
    }

    private static function parseBool($data)
    {
        if(is_bool($data))
        {
            return $data;
        }
        elseif(is_string($data))
        {
            return boolval($data);
        }
        return null;
    }

    /**
     * @param $data array|object
     * @param $conf ConfNode
     * @return array
     */
    private static function parseArray($data, $conf)
    {
        if(empty($data))
        {
            return null;
        }
        $value = [];
        if(!empty($conf->class))
        {
            $arrayItemConf = $conf->copy();
            $arrayItemConf->type = ConfSymbols::TypeObject;
            $arrayItemConf->selector = "";
        }

        foreach($data as $key=> $item)
        {
            if(isset($arrayItemConf))
            {
                $obj = self::queryValue($arrayItemConf,$item);
                if(!empty($obj))
                {
                    $value[] = $obj;
                }
                continue;
            }
            $arrayItem = [];
            $allEmpty = true;
            foreach($conf->fields as $fConf)
            {
                $field = self::queryValue($fConf,$item);
                $arrayItem[$fConf->name] = $field;
                if(!empty($field))
                {
                    $allEmpty = false;
                }
            }
            if(!$allEmpty)
            {
                $value[] = $arrayItem;
            }
        }
        return $value;
    }

    /**
     * @param $data array|object
     * @param $conf ConfNode
     * @return null
     */
    protected static function parseObject($data,$conf)
    {
        $value = new $conf->class();
        $isAllFieldEmpty = true;
        /** @var ConfNode $item */
        foreach($conf->fields as $item)
        {
            $fieldName = $item->name;
            $fieldValue = self::queryValue($item,$data);
            $value->$fieldName = $fieldValue;
            if(!empty($fieldValue))
            {
                $isAllFieldEmpty = false;
            }
        }
        if($isAllFieldEmpty)
        {
            return null;
        }
        return $value;
    }
}