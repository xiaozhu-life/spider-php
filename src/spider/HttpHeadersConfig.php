<?php
/**
 * Created by PhpStorm.
 * User: liyulong
 * Date: 16/1/21
 * Time: 下午9:38
 */

namespace xiaozhu\spider;


class HttpHeadersConfig
{
    public function __construct($randomInit = true)
    {
        $this->accpetLanguage()
            ->setUserAgent()
            ->setIp()
            ->cache()
            ->setRefer()
            ->useCompress();
    }

    /** @var array  */
    protected $headers = [
        "User-Agent"=>"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.86 Safari/537.36"
    ];

    public function getHeaders()
    {
        return $this->headers;
    }

    public function addHeader($key,$value)
    {
        $this->headers[$key] = $value;
    }

    public function addHeaders($headers)
    {
        foreach($headers as $k=>$v)
        {
            $this->addHeader($k,$v);
        }
    }

    public function accpetLanguage($language="zh-CN,zh;q=0.8,en;q=0.6")
    {
        $this->addHeader("Accept-Language",$language);
        return $this;
    }

    public function keepAlive()
    {
        $this->addHeader("Connection","keep-alive");
        return $this;
    }

    public function useCompress($option = true)
    {
        if ($option) {
            $this->addHeader("Accept-Encoding", "compress, gzip, deflate, sdch");
        }
        return $this;
    }

    public function setRefer($refer = null)
    {
        if(empty($refer))
        {
            $refer = "http://www.baidu.com";
        }
        $this->addHeader("Referer",$refer);
        return $this;
    }

    public function setCookie($cookie)
    {
        $this->addHeader("Cookie",$cookie);
        return $this;
    }

    /**
     * @param $cookies [key=>value,key=>value]
     * @return $this
     */
    public function setCookies($cookies)
    {
        if(empty($cookies))
        {
            return $this;
        }
        $cookieStrSet = [];
        foreach($cookies as $k=>$v)
        {
            if(is_string($k))
            {
                $cookieStrSet[] = "$k=$v";
            }
            elseif(is_int($k))
            {
                $cookieStrSet[]=$v;
            }
        }
        return $this->setCookie(implode("; ",$cookieStrSet));
    }

    public function setUserAgent($userAgent = '')
    {
        if(empty($userAgent))
        {
            $userAgent = self::$agents[mt_rand(0 , count(self::$agents)-1 )];
        }
        $this->addHeader("User-Agent",$userAgent);
        return $this;
    }

    public function setIp($ip = '')
    {
        if(empty($ip))
        {
            $ip = $this->generate_cn_ip();
        }
        $this->addHeaders(["CLIENT-IP"=>$ip,"X-FORWARDED-FOR"=>$ip]);
        return $this;
    }

    /**
     * @param $authInfo
     * @return $this
     */
    public function authorization($authInfo)
    {
        $this->addHeader("Authorization",$authInfo);
        return $this;
    }

    /**
     * @param string $way nocache 只要最新的; max-age 只接受 Age 值小于 max-age 值，并且没有过期的对象
     * @return $this
     */
    public function cache($way="no-cache")
    {
        $this->addHeader("Cache-Control",$way);
        return $this;
    }

    private static  $agents = [
        "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.35 (KHTML, like Gecko) Chrome/27.0.1444.3 Safari/537.35",
        "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:42.0) Gecko/20100101 Firefox/42.0",
        "Mozilla/5.0 (Macintosh; PPC Mac OS X; U; en) Opera 8.0",
        "Mozilla/5.0 (Windows; U; Windows NT 5.2) AppleWebKit/525.13 (KHTML, like Gecko) Version/3.1 Safari/525.13",
        "Mozilla/5.0 (Windows; U; Windows NT 5.2) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.2.149.27 Safari/525.13",
        "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.12) Gecko/20080219 Firefox/2.0.0.12 Navigator/9.0.0.6",
        "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.86 Safari/537.36",
    ];

    /**
     * 生成国内ip
     * @return string 返回ip
     */
    protected function generate_cn_ip(){
        $ip_long = array(
            array('607649792', '608174079'), //36.56.0.0-36.63.255.255
            array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255
            array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
            array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
            array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
            array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
            array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
            array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
            array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
            array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255
        );
        $rand_key = mt_rand(0, 9);
        $ip= long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
        return $ip;
    }

}