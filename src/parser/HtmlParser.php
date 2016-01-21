<?php
/**
 * Created by PhpStorm.
 * User: Li Yulong
 * Date: 2015/12/2
 * Time: 12:55
 */

namespace xiaozhu\spider\parser;
use xiaozhu\spider\parser\conf\ConfNode;
use xiaozhu\spider\parser\conf\ConfSymbols;
use xiaozhu\spider\helpers\CmdTrait;
use xiaozhu\spider\helpers\StringHelper;
use DOMDocument;
use DOMElement;
use phpQuery as query;
use phpQueryObject;

class HtmlParser
{
    use CmdTrait;
    /**
     * @param $conf ConfNode
     * @param $html
     * @return array|float|int|String
     */
    public static function parseHtml($conf,$html)
    {
        query::$defaultCharset = "utf-8";
        $doc = query::newDocument($html);
        query::selectDocument($doc);
        $value = self::queryValue(pq($doc),$conf);
        //清理内存
        query::unloadDocuments($doc);
        return $value;
    }

    /**
     * @param $query phpQueryObject
     * @param $conf ConfNode
     * @return array|String|int|float
     */
    protected static function queryValue($query,$conf)
    {
        if(!empty($conf->selector))
        {
            if($query instanceof DOMElement)
            {
                /** @var DOMElement $item */
                $item = $query;
                $query = pq($item);
            }
            if($query instanceof DOMDocument)
            {
                /** @var DOMDocument $item */
                $item = $query;
                $query = pq($item);
            }
            $query = $query->find($conf->selector);
            if($query->length === 0)
            {
                self::i($conf->name . " is empty:" . $conf->selector . (empty($conf->attr) ? "" : "-") . $conf->attr,3);
                return $conf->default;
            }
        }
        switch($conf->type)
        {
            case ConfSymbols::TypeInt:
                return self::parseInt($query,$conf);
            case ConfSymbols::TypeFloat:
                return self::parseFloat($query,$conf);
            case ConfSymbols::TypeString:
                return self::parseString($query,$conf);
            case ConfSymbols::TypeBool:
                return self::parseBool($query,$conf);
            case ConfSymbols::TypeArray:
                return self::parseArray($query,$conf);
            case ConfSymbols::TypeObject:
                return self::parseObject($query,$conf);
            case ConfSymbols::TypeNull:
            default:
                break;
        }
        return null;
    }

    /**
     * @param $query phpQueryObject
     * @param $conf ConfNode
     * @return null
     */
    protected static function parseObject($query,$conf)
    {
        $value = new $conf->class();
        $isAllFieldEmpty = true;
        /** @var ConfNode $item */
        foreach($conf->fields as $item)
        {
            $fieldName = $item->name;
            $fieldValue = self::queryValue($query,$item);
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

    /**
     * @param $query phpQueryObject
     * @param $conf ConfNode
     * @return array
     */
    protected static function parseArray($query,$conf)
    {
        $value = [];
        if(!empty($conf->class))
        {
            $arrayItemConf = $conf->copy();
            $arrayItemConf->type = ConfSymbols::TypeObject;
            $arrayItemConf->selector = "";
        }
        /** @var  DOMElement $item */
        foreach($query as $item)
        {
            if(isset($arrayItemConf))
            {
                $obj = self::queryValue($item,$arrayItemConf);
                if(!empty($obj))
                {
                    $value[] = $obj;
                }
                continue;
            }
            $arrayItem = [];
            $allEmpty = true;
            if(count($conf->fields) == 1 && empty($conf->fields[0]->name))//没有字段名的时候
            {
                $value[] = self::queryValue($item,$conf->fields[0]);
                continue;
            }
            foreach($conf->fields as $fConf)
            {
                $field = self::queryValue($item,$fConf);
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
     * @param $query phpQueryObject
     * @param $conf ConfNode
     * @return string
     */
    protected static function parseString($query,$conf)
    {
        if($query instanceof DOMElement)
        {
            $query = pq($query);
        }
        if(!empty($conf->attr))
        {
            if($conf->attr == "text")
            {
                $value = $query->text();
            }
            elseif($conf->attr == "html")
            {
                $value = $query->htmlOuter();
            }
            else
            {
                $value = $query->attr($conf->attr);
            }
        }
        else
        {
            $value = $query->text();
        }
        return StringHelper::trimAll($value);
    }

    /**
     * @param $query phpQueryObject
     * @param $conf ConfNode
     * @return int
     */
    protected static function parseInt($query,$conf)
    {
        $value = self::parseString($query,$conf);
        return intval($value);
    }

    /**
     * @param $query phpQueryObject
     * @param $conf ConfNode
     * @return double
     */
    protected static function parseFloat($query,$conf)
    {
        $value = self::parseString($query,$conf);
        return doubleval($value);
    }

    /**
     * @param $query phpQueryObject
     * @param $conf ConfNode
     * @return string
     */
    protected static function parseBool($query,$conf)
    {
        $value = self::parseString($query,$conf);
        return $value;
    }

}