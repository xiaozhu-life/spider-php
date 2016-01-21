<?php
/**
 * Created by PhpStorm.
 * User: Li Yulong
 * Date: 2015/11/19
 * Time: 17:37
 */

namespace xiaozhu\spider\helpers;


use common\components\ConfigComponent;
use common\models\Source;
use common\models\SourceSubset;
use yii\db\Exception;

class CrawlerHelper
{
    use CmdTrait;
    public static function convert2Utf8($content)
    {
        return mb_convert_encoding($content,'UTF-8','UTF-8,GBK,GB2312,ASCII,JIS,EUC-JP,SJIS');
    }

}