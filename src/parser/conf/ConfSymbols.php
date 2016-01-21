<?php
/**
 * Created by PhpStorm.
 * User: Li Yulong
 * Date: 2015/12/2
 * Time: 10:06
 */

namespace xiaozhu\spider\parser\conf;


class ConfSymbols
{
    //表明数据类型的一些配置项
    //类型
    const Type = "type";
    //类名 包含命名空间的
    const ClassName = "class";
    //字段名
    const Name = "name";
    //字段开始的配置
    const Fields = "fields";

    //xpath的值
    const Path = "path";
    //css selector的值
    const Selector = "selector";
    //默认值
    const DefaultValue = "default";



    //int类型
    const TypeInt = "int";
    //float类型
    const TypeFloat = "float";
    //bool类型
    const TypeBool = "bool";
    //string 类型
    const TypeString = "string";

    //obj
    const TypeObject = "object";
    //数组类型
    const TypeArray = "array";
    //空类型
    const TypeNull = "null";

    //空值
    const ValueNull = "null";
    const ValueZero = '0';
    const ValueEmptyString = '';
    const ValueEmptyArray = '[]';
    const ValueFalse = "false";
    const ValueTrue = "true";

    const JsonPathSeperator=".";

    const TypesConfig = [
        self::TypeFloat => [self::ValueNull,self::ValueZero],
        self::TypeInt   => [self::ValueNull,self::ValueZero],
        self::TypeBool  => [self::ValueFalse,self::ValueTrue],
        self::TypeString => [self::ValueNull,self::ValueEmptyString],

        self::TypeObject => [self::ValueNull],
        self::TypeArray  => [self::ValueNull],

        self::TypeNull => [self::ValueNull],
    ];

    const ValueConfig = [
        self::ValueEmptyString => '',
        self::ValueEmptyArray  => [],
        self::ValueZero  => 0,
        self::ValueNull  => null,
        self::ValueFalse => false,
        self::ValueTrue  => true,
    ];
}