<?php
    header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Shanghai');
//设置起始时间
$five = strtotime(date('Y-m-d',time()).'16:59:59');
//设置结束时间
$seven = strtotime(date('Y-m-d',time()).'17:00:00');
    $ip = GetIp();
    //$ip = "219.159.235.101"; //广西IP测试使用
    $spread = "index_guanggao.php"; //推广页
    $company = "index_shenhe.php"; //审核页

    //PC全国打开是审核页
    //移动端打开是推广页 => +屏蔽北京/广州打开是审核页)


    /**
    注意，下面为需要限制移动端的地区为数组。
    如果没有就使用$shield_cities= array();//不限制
    如果需要的就根据你的需要做限制,如：
    $shield_cities= array('广州', '北京')//限制移动端的地区
    需要把相应的一行前面的//去掉，不用的就加上//
    */
    //配置要被屏蔽的城市


//判断时间	
if(time() > $five && time() < $seven){
//时间内输出
$f=file_get_contents($company);
}
else{
	//时间外判断
    //$shield_cities = array('广州', '北京', '武汉',  '天津' ,  '深圳');
    //不屏蔽任何地区
    $shield_cities = array();
	//不屏蔽PC端
	//  if (!IsArea($shield_cities) ) {
    if ( isMobile() && !IsArea($shield_cities) ) {
        //要上的广告页面
         
    $f=file_get_contents($spread);
    }else{
        //这里是审核页
    $f=file_get_contents($company);
    }
}
    echo $f;

//判断是否在屏蔽地区
function IsArea($showArea = array('广州','武汉', '北京',  '天津' ,  '深圳'))
{
    if (empty($showArea)) return false;
    $ip = getIP();
    include("locationSelect.class.php");
    $IpLocation = new IpLocation();
    $data = $IpLocation->getlocation($ip);
    $country = iconv("GBK", "UTF-8", $data['country']);
    foreach ($showArea as $key => $value) {
        //判断当前是否在屏蔽城市
        if (strstr($country,$value)) {
            return true;
            break;
        }
    }
    return false;
}


/*获取客户端ip*/
    function getIP(){  
        $realip = '';  
        $unknown = 'unknown';  
        if (isset($_SERVER)){  
            if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)){  
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);  
                foreach($arr as $ip){  
                    $ip = trim($ip);  
                    if ($ip != 'unknown'){  
                        $realip = $ip;  
                        break;  
                    }  
                }  
            }else if(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) && strcasecmp($_SERVER['HTTP_CLIENT_IP'], $unknown)){  
                $realip = $_SERVER['HTTP_CLIENT_IP'];  
            }else if(isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)){  
                $realip = $_SERVER['REMOTE_ADDR'];  
            }else{  
                $realip = $unknown;  
            }  
        }else{  
            if(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), $unknown)){  
                $realip = getenv("HTTP_X_FORWARDED_FOR");  
            }else if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), $unknown)){  
                $realip = getenv("HTTP_CLIENT_IP");  
            }else if(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), $unknown)){  
                $realip = getenv("REMOTE_ADDR");  
            }else{  
                $realip = $unknown;  
            }  
        }  
        $realip = preg_match("/[\d\.]{7,15}/", $realip, $matches) ? $matches[0] : $unknown;  
        return $realip;  
    } 

function isMobile() {
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset($_SERVER['HTTP_VIA'])) {
        //找不到为flase,否则为true
        if(stristr($_SERVER['HTTP_VIA'], "wap"))
        {
            return true;
        }
    }
    //脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array (
            'nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'mobile',
            'phone',
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    //协议法，因为有可能不准确，放到最后判断
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}