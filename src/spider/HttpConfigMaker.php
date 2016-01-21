<?php
namespace xiaozhu\spider;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;

/**
 * Created by PhpStorm.
 * User: liyulong
 * Date: 16/1/20
 * Time: 下午8:51
 */
abstract class HttpConfigMaker
{
    /**
     * @param $url
     * @param null $data
     * @return Request
     */
    public abstract function makeRequest($url,$type,$data = null);

    protected $requestConfig = [
        RequestOptions::DECODE_CONTENT=>true,
        RequestOptions::TIMEOUT=>60,
        RequestOptions::CONNECT_TIMEOUT=>60,
    ];

    protected function setTimeout($timeout)
    {
        $this->requestConfig[RequestOptions::TIMEOUT] = $timeout;
    }

    protected function setConnectionTimeout($timeout)
    {
        $this->requestConfig[RequestOptions::CONNECT_TIMEOUT] = $timeout;
    }
}