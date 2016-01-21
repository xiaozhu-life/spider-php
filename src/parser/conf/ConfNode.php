<?php
/**
 * Created by PhpStorm.
 * User: Li Yulong
 * Date: 2015/12/2
 * Time: 10:22
 */

namespace xiaozhu\spider\parser\conf;

class ConfNode
{
    /** @var  string */
    public $type;
    /** @var  string class name */
    public $class;
    /** @var  string */
    public $name;
    /** @var  string */
    public $selector;
    /** @var  ConfNode[] */
    public $fields;
    /** @var  mixed */
    public $default = null;

    public $attr = null;

    public function validate()
    {
        $this->errors = [];
        if($this->type !== ConfSymbols::Type && empty($this->selector))
        {
            $this->error("$this->name selector为空");
        }
        if(empty($this->type))
        {
            $this->type = ConfSymbols::TypeString;
        }
        $values = ConfSymbols::TypesConfig;
        $thisTypeValues = $values[$this->type];

        //如果不是对象或者数组,class和field都应该为空
        if(!in_array($this->type,[ConfSymbols::TypeObject,ConfSymbols::TypeArray]))
        {
            if(!empty($this->class) || !empty($this->fields))
            {
                $this->error("$this->name $this->type 的class项和fields项应该为空");
            }
        }
        if($this->type == ConfSymbols::TypeObject)
        {
            if(empty($this->class) || empty($this->fields))
            {
                $this->error("$this->name $this->type 的class项和fields项应该非空");
            }
        }

        //验证默认值
        switch($this->type)
        {
            case ConfSymbols::TypeInt:
                if(!empty($this->default) && !is_int($this->default))
                {
                    $this->error("$this->name $this->type 默认值应该为数字");
                }
                break;
            case ConfSymbols::TypeBool:
                if(!empty($this->default) && !in_array($this->default,$thisTypeValues))
                {
                    $this->error("$this->name $this->type 默认值应该为false or true");
                }
                if(empty($this->default))
                {
                    $this->default = false;
                }
                break;
            case ConfSymbols::TypeString:
                break;
            case ConfSymbols::TypeFloat:
                if(!empty($this->default) && !(is_int($this->default) || is_float($this->default)))
                {
                    $this->error("$this->name $this->type 默认值应该为数字");
                }
                break;
            case ConfSymbols::TypeObject:
                if(!class_exists($this->class))
                {
                    throw new \Exception("$this->class not exists");
                }
                break;
            case ConfSymbols::TypeArray:
                break;
        }
        return empty($this->errors);
    }

    public function copy()
    {
        $other = new ConfNode();
        foreach(self::getConfAttributes() as $k=>$v)
        {
            $other->$k = $this->$k;
        }
        return $other;
    }

    /**
     * @param $data array
     * @param $parentNode ConfNode
     * @return ConfNode
     * @throws \Exception
     */
    public static function loadFromArray($data,$parentNode = null)
    {
        $root = new ConfNode();
        $root->parentNode = $parentNode;

        $root->type = self::getType($data);
        $root->name = self::getName($data,$parentNode);
        self::setClass($root,$data);
        self::setFields($root,$data);
        self::setSelector($root,$data);

        if(isset($data[ConfSymbols::DefaultValue]))
        {
            $root->default = $data[ConfSymbols::DefaultValue];
        }
        return $root;
    }

    /**
     * @param $data array
     * @param $node ConfNode
     */
    protected static function setSelector($node,$data)
    {
        if(isset($data[ConfSymbols::Selector]))
        {
            $selector = $data[ConfSymbols::Selector];
            $pos = strpos($selector,"{");
            if($pos !== false)
            {
                $node->selector = substr($selector,0,$pos);
                $node->attr = substr($selector,$pos + 1,strlen($selector) - $pos - 2);
            }
            else
            {
                $node->selector = $selector;
            }
        }
        else
        {
            $node->attr = "text";
        }
    }

    /**
     * @param $node ConfNode
     * @param $data array
     */
    protected static function setFields(&$node,$data)
    {
        if(in_array($node->type,[ConfSymbols::TypeArray,ConfSymbols::TypeObject]) && isset($data[ConfSymbols::Fields]))
        {
            $node->fields = [];
            foreach($data[ConfSymbols::Fields] as $k=>$item)
            {
                if(is_string($k))
                {
                    if(is_array($item) && !isset($item[ConfSymbols::Name]))
                    {
                        $item[ConfSymbols::Name] = $k;
                    }
                    if(is_string($item))
                    {
                        $item = [ConfSymbols::Name => $k,ConfSymbols::Selector=>$item];
                    }
                }
                $fieldItem = self::loadFromArray($item,$node);
                if(!empty($fieldItem))
                {
                    $node->fields[] = $fieldItem;
                }
            }
        }
    }

    /**
     * @param $node ConfNode
     * @param $data array
     * @throws \Exception
     */
    protected static function setClass(&$node,$data)
    {
        if($node->type == ConfSymbols::TypeObject || $node->type == ConfSymbols::TypeArray)
        {
            if(isset($data[ConfSymbols::ClassName]))
            {
                $node->class = $data[ConfSymbols::ClassName];
            }
            elseif($node->type == ConfSymbols::TypeObject)
            {
                throw new \Exception(" class name 没有配置:".var_export($data));
            }
        }

    }

    /**
     * @param $data array
     * @return string
     */
    protected static function getType($data)
    {

        if(isset($data[ConfSymbols::Type]))
        {
            return $data[ConfSymbols::Type];
        }
        $types = array_keys(ConfSymbols::TypesConfig);
        if(isset($data[0]) && in_array($data[0],$types))
        {
            return $data[0];
        }
        if(isset($data[1]) && in_array($data[1],$types))
        {
            return $data[1];
        }
        return ConfSymbols::TypeString;
    }

    /**
     * @param $data
     * @param $parentNode
     * @return null
     * @throws \Exception
     */
    protected static function getName($data,$parentNode)
    {

        if(isset($data[ConfSymbols::Name]))
        {
            return $data[ConfSymbols::Name];
        }
        if(isset($data[0]) && !in_array($data[0],[ConfSymbols::TypeObject,ConfSymbols::TypeArray]))
        {
            return $data[0];
        }
        elseif(isset($data[1]) && !in_array($data[1],[ConfSymbols::TypeObject,ConfSymbols::TypeArray]))
        {
            return  $data[1];
        }
        if(empty($parentNode))
        {
            return null;
        }
        else
        {
            throw new \Exception(" name 没有配置 : ".var_export($data));
        }
    }

    private static function getConfAttributes()
    {
        return [
            'type'=>ConfSymbols::Type,
            'class'=>ConfSymbols::ClassName,
            'name'=>ConfSymbols::Name,
            'selector'=>ConfSymbols::Selector,
            'fields'=>ConfSymbols::Fields,
            'default'=>ConfSymbols::DefaultValue
        ];
    }
    /** @var  ConfNode */
    public $parentNode;
    /** @var  [] 记录错误 */
    public $errors;
    private function error($info)
    {
        $this->errors[] = $info;
    }

}