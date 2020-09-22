<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/14
 * Time: 9:26
 * CURL设置请求类
 */
namespace app\controller;
use think\Controller;

Class HttpCurl extends Controller
{
    public $host;
    public $port;
    public $path;
    public $method;
    public $postdata = '';
    public $cookiefile = "";//服务器绝对路径
    public $referer;
    public $accept = 'text/xml,application/xml,application/xhtml+xml,text/html,text/plain,image/png,image/jpeg,image/gif,*/*';
    public $content_type = 'application/json';
    public $user_agent = "Koala Admin";
    public $authorization = "e3f14076-571e-4243-b315-bbc4d34547d4";
    public $timeout = 20;
    public $use_gzip = true;
    public $persist_cookies = true;

    //初始化方法
    protected function initialize()
    {
        header("Content-type: text/html; charset=utf-8");
        $this->host = config('domain_name');
        $this->setUserAgent('Koala Admin');
    }

    //GET请求方式
    function get($path, $data=[])
    {
        $this->path = $path;
        if (!empty($data) && count($data)>0)
        {
            $this->path .= '?'.$this->buildGetQuery($data);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->path);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_CURLOPT_HTTPHEADERsetopt($ch, CURLOPT_COOKIEFILE, $this->cookiefile);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($this->postdata),
                'Authorization: '.$this->Authorization
            )
        );
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    //PUT请求方式
    function put($path, $data)
    {
        $this->path = $path;
        if (!empty($data)|| count($data)>0)
        {
            $this->postdata = $this->buildJsonQuery($data);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->path);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$this->postdata);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiefile);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($this->postdata),
                'Authorization: '.$this->Authorization
            )
        );
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    //DELETE请求方式
    function delete($path, $data=null)
    {
        $this->path = $path;
        if (!empty($data)|| count($data)>0)
        {
            $this->postdata = $this->buildJsonQuery($data);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->path);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$this->postdata);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiefile);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($this->postdata),
                'Authorization: '.$this->Authorization
            )
        );
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     *post请求方式
     */
    public function post($path, $data =[]){
        $this->postdata = $this->buildJsonQuery($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($this->postdata),
                'Authorization: '.$this->Authorization
            )
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_COOKIEJAR,  $this->cookiefile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiefile);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    //=================================================方法================================================//

    //球球数据转化方法
    public function buildGetQuery($data)
    {
        $querystring = '';
        if (is_array($data)) {

            foreach ($data as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $val2) {
                        $querystring .= urlencode($key).'='.urlencode($val2).'&';
                    }
                } else {
                    $querystring .= urlencode($key).'='.urlencode($val).'&';
                }
            }
            $querystring = substr($querystring, 0, -1); // Eliminate unnecessary &
        } else {
            $querystring = $data;
        }
        return $querystring;
    }


    //Json数据格式化方法
    function buildJsonQuery($data)
    {
        $querystring = '';
        if (is_array($data)) {
            $querystring=json_encode($data);
        }
        else echo "Parameters should be array.";
        return $querystring;
    }

    //对象转化成数组方法
    function object_array($array)
    {
        if(is_object($array))
        {
            $array = (array)$array;
        }
        if(is_array($array))
        {
            foreach($array as $key=>$value)
            {
                $array[$key] = $this->object_array($value);
            }
        }
        return $array;
    }

    //设置UserAgent
    private function setUserAgent($string)
    {
        $this->user_agent = $string;
    }

    //设置cookie
    private function setcookiefile($filepath)
    {
        $this->cookiefile = realpath($filepath);
    }

    //获取cookie文件路径
    private function getcookie()
    {
        if(file_exists($this->cookiefile))
        {
            $content = file_get_contents($this->cookiefile);
            return $content;
        }
        else return null;
    }
}