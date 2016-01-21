<?php
/**
 * Created by PhpStorm.
 * User: Li Yulong
 * Date: 2015/12/3
 * Time: 11:33
 */

namespace xiaozhu\spider\parser;


use xiaozhu\spider\parser\conf\ConfNode;

class ParserEngine
{
    /** @var  ConfNode */
    private $conf;
    private $type;
    const TYPE_HTML = "html";
    const TYPE_JSON = "json";

    /**
     * @param $conf array
     */
    public function __construct($conf)
    {
        $this->setConf($conf);
    }

    /**
     * @param $conf array
     */
    public function setConf($conf)
    {
        $this->conf = ConfNode::loadFromArray($conf);
    }

    /**
     * @param $type string
     */
    public function setType($type)
    {
        $this->type = $type;
    }
    public function parse($content)
    {
        return self::parseContent($content,$this->conf,$this->type);
    }

    /**
     * @param $content
     * @param $conf
     * @param null $type
     * @return array|float|int|String
     */
    public static function parseContent($content,$conf,$type = null)
    {
        if(empty($content))
        {
            echo "empty content";
        }
        if(empty($type))
        {
            $type = self::TYPE_HTML;
        }
        if($type === self::TYPE_HTML)
        {
            return HtmlParser::parseHtml($conf,$content);
        }
        elseif($type === self::TYPE_JSON)
        {
            return JsonParser::parseJson($conf,$content);
        }
    }
}