<?php
/**
 * 用来遍历字符串,可以使用foreach遍历
 * 目前只支持UTF-8
 * Created by PhpStorm.
 * User: Li Yulong
 * Date: 2015/12/17
 * Time: 16:58
 */

namespace xiaozhu\spider\helpers;


use Iterator;

class StringChars implements Iterator
{

    private $str;
    private $encode = "UTF-8";
    private $pos = 0;
    private $len = 0;
    /**
     * @param $str string utf-8
     */
    public function __construct($str)
    {
        $this->str = $str;
        $this->len = mb_strlen($str);
    }
    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return mb_substr($this->str,$this->pos,1,$this->encode);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->pos++;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->pos;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->pos < $this->len;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->pos = 0;
    }

    /**
     * @param $needle string
     * @param $endOfNeedle bool 是否停留在needle的尾部 默认为true
     * @return bool|int
     */
    public function skipToNextMatch($needle,$endOfNeedle = true)
    {
        $str = $this->str;
        $res = mb_strpos($str,$needle,$this->pos);
        if($res === false)
        {
            return false;
        }
        $this->pos = $res;
        if($endOfNeedle)
        {
            $this->pos = $this->pos + mb_strlen($needle);
        }
        return $this->pos;
    }
}