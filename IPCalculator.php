<?php
header('Content-Type:application/json; charset=utf-8');
// // ip正则匹配
// echo preg_match('/((1\d{2}|25[0-5]|2[0-4]\d|[1-9]?\d)\.){3}(25[0-5]|2[0-4]\d|1\d{2}|[1-9]?\d)$/', '255.255.255.255');
// echo '<br>';
// echo long2ip(3402113154);
// echo ip2long('255.255.255.255');
// echo '<br>';

// // ip转换成长整形10进制
// function ipToLong($ip='0.0.0.0'){
// 	if (!$ip) {
// 		return '请输入ip';
// 	}

// 	$ip = explode('.', $ip);
// 	$ip = array_reverse($ip);//数组反转
// 	$r = 0;
// 	for($i=0,$j=count($ip); $i<$j; $i++){
// 		$r += $ip[$i] * pow(256, $i);
// 	}
// 	$r = sprintf("%u", $r);
// 	return $r;
// }

// echo ipToLong('10.0.0.0');
// echo '<br>';

// 检测IP段函数
function isIPParagraph($IPParagraph='0.0.0.0/32'){
	// 匹配ip段，如：0.0.0.0/32
	$isIP = preg_match('/(((1\d{2}|25[0-5]|2[0-4]\d|[1-9]?\d)\.){3}(25[0-5]|2[0-4]\d|1\d{2}|[1-9]?\d))\/([1-9]|[1-2][0-9]|3[0-2])$/', $IPParagraph);
	if ($isIP) {
		return true;
	}
	return false;
}
// echo isIPParagraph('12.12.12.12/2');
// echo '<br>';

function getIPInfo($IPParagraph='0.0.0.0/32'){
	if (!$IPParagraph) {
		return '请输入IP段';
	}
	$isTrue=isIPParagraph($IPParagraph);
	if (!$isTrue) {
		return '无效IP段';
	}
	$IPParagraphArray = explode('/', $IPParagraph);
	$mark = $IPParagraphArray[1]; // 获取IP段的掩码位
	$dilatation = pow(2, 32-$mark); // 最多可以容纳的主机数
	$usable = $dilatation-2; // 可供使用的主机数
	$subnetMask = long2ip(ip2long('255.255.255.255')-($dilatation-1)); // 子网掩码
	$subnetMaskArray = explode('.', $subnetMask);
	$ipArray = explode('.', $IPParagraphArray[0]);
	if ($subnetMaskArray[2] === '255') { // ip所处子网掩码判断某一位是在哪个类别下
		$category = 'C类';
		$flag=array(3); // 标识ip哪位为0的数组
	}elseif ($subnetMaskArray[2] < '255' && $subnetMaskArray[1] === '255') {
		$category = 'B类';
		$flag=array(2,3);
	}elseif ($subnetMaskArray[1] < '255') {
		$category = 'A类';
		$flag=array(1,2,3);
	}
	for ($i=0; $i < count($flag); $i++) {
		$subnet = pow(2, 8*($i+1)-(32-$mark)); // 子网个数
		$count = $subnet===1 ? pow(2, 8*($i+1)) : $subnet; // 当子网等于1，则说明是8,16,24，分别是a，b，c类
		$ipArray[$flag[$i]] = 0; // 将对等的类别下ipArray数组中的某个下标的值清零
	}

	$ipString = implode('.', $ipArray); // 将原ip转化成x.x.x.0的形式
	$iplong = ip2long($ipString); // ip转长整型
	$subnetInit = array();
	for ($i=0; $i < $subnet; $i++) {
		$subnetInit[$i] = long2ip($iplong+($dilatation*$i)); // 循环算出每个子网的第一个ip
	}

	$dilatationIP = array();
	for ($i=0; $i < $count; $i++) {
		if ($subnet === 1) { // 当子网只有一个时，循环当前类别的总个数
			$dilatationIP[$i] = long2ip(ip2long($subnetInit[0])+$i);
		}elseif(isset($subnetInit[$i+1])){
			if (ip2long($IPParagraphArray[0]) >= ip2long($subnetInit[$i]) && ip2long($IPParagraphArray[0]) < ip2long($subnetInit[$i+1])) {
				for ($j=0; $j < $dilatation; $j++) {
					$dilatationIP[$j] = long2ip(ip2long($subnetInit[$i])+$j); // ip在子网里所有的ip数
				}
				break;
			}
		}else{
			if (ip2long($IPParagraphArray[0]) >= ip2long($subnetInit[$i])) {
				for ($j=0; $j < $dilatation; $j++) {
					$dilatationIP[$j] = long2ip(ip2long($subnetInit[$i])+$j); // ip在子网里所有的ip数
				}
				break;
			}
		}

	}

	$rs = array(
		'ip'			=>	$IPParagraphArray[0],
		'mark'			=>	$mark,
		'dilatation'	=>	$dilatation,
		'usable'		=>	$usable,
		'dilatationIP'	=>	$dilatationIP,
		'networkAddr'	=>	$dilatationIP[0],
		'roadcastAddr'	=>	$dilatationIP[$dilatation-1],
		'subnet'		=>	$subnet,
		'subnetInit'	=>	$subnetInit,
		'subnetMask'	=>	$subnetMask,
		'category'		=>	$category,
	);
	// return json_encode($rs, JSON_UNESCAPED_UNICODE);
	return $rs;
}

print_r(getIPInfo('14.18.225.127/26'));
// print_r(getIPInfo('192.168.1.53/27'));
