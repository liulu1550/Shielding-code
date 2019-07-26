<?php
$result = json_decode(get_info(),true);
if($result){
    //remote_ip_info = remote_ip_info.data;
    //remote_ip_info.city =='北京'
    $remote = $result['data'];

	$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
	
	// 屏蔽城市 city  北京  上海
	//屏蔽省份  region  安徽 河南 四川
	// 电信运营商 isp  电信 联通 移动
	
	
	$uachar = '/(nokia|sony|ericsson|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|mobile)/i';  // 移动端UA
	
	// 屏蔽移动端  就在城市筛选后面加上      && (($ua == '') || preg_match($uachar, $ua))
    if(($remote['region'] !='安徽' && $remote['region'] !='上海' && $remote['region'] !='广东' && $remote['region'] !='河南')){
        include 'index_ruanwen.html'; // 软文页
    }else{
		
		include '../index.html';   // 审核页
    }
}

function get_info(){
    $url ="http://ip.taobao.com/service/getIpInfo.php?ip=".get_ip();
    $ch = curl_init();
    //设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //执行并获取HTML文档内容
    $output = curl_exec($ch);
    //释放curl句柄
    curl_close($ch);
    return $output;
}
//不同环境下获取真实的IP
function get_ip(){
    //判断服务器是否允许$_SERVER
    if(isset($_SERVER)){
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }elseif(isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        }else{
            $realip = $_SERVER['REMOTE_ADDR'];
        }
    }else{
        //不允许就使用getenv获取
        if(getenv("HTTP_X_FORWARDED_FOR")){
            $realip = getenv( "HTTP_X_FORWARDED_FOR");
        }elseif(getenv("HTTP_CLIENT_IP")) {
            $realip = getenv("HTTP_CLIENT_IP");
        }else{
            $realip = getenv("REMOTE_ADDR");
        }
    }

    return $realip;
}