<?php
header("Content-type: text/javascript; charset=utf-8");
/*
 * Tangram
 * Copyright 2009 Baidu Inc. All rights reserved.
 *
 * path: import.php
 * author: berg
 * version: 1.0
 * date: 2010/07/18 23:57:52
 *
 * @fileoverview * import.js的php版本
 * 接受一个f参数，格式和import.js相同，自动合并js并输出
 * 此外，本脚本支持引入一个包所有文件（其实也就是一个目录下的所有js文件，**不递归**）
 * IE下，get请求不能超过2083字节，请注意。
 */

$DEBUG = false;
$cov = $_GET['cov'];
$f = explode(',', $_GET['f']);//explode() 函数把字符串分割为数组,此处$f=baidu.ajax.form
$e = (array_key_exists('e', $_GET) && $_GET['e']!='') ? explode(",", $_GET['e']) : array();
require_once 'analysis.php';
$analysis = new Analysis();
$IGNORE = array();
foreach ($e as $d){
	$IGNORE = array_merge($IGNORE, array_keys($analysis->get_import_srcs($d)));
}
if($DEBUG)var_dump($IGNORE);

$cnt = "";
if($cov){//覆盖率
	$filepath = '../../../test/coverage/';
	foreach($f as $d){
       getIGNORE($d);
       foreach($IGNORE as $l){
       	 $path = join('/', explode('.', $l)).'.js';
       	 if(file_exists($filepath.$path)){
			$cnt.= file_get_contents($filepath.$path);
         }
       }
	}
}
else {
	foreach($f as $d){
		$cnt.=importSrc($d);
	}
}
echo $cnt;

//组装数组$IGNORE，按加载顺序存所有的导入接口名
function getIGNORE($d){
	global $IGNORE;
	global $cntcov;
	$ccnt = Analysis::get_src_cnt($d);
	$num = 0;
	$array_length = count($ccnt['i']);
	foreach($ccnt['i'] as $is){
		$num++;
		getIGNORE($is);
		if($array_length==$num){
	        if(!in_array($is,$IGNORE)){
	    		array_push($IGNORE, $is);
	    	}
	    	if(!in_array($d,$IGNORE)){
	    		array_push($IGNORE, $d);
	    	}
	    }
	}
}

function importSrc($d){
	global $IGNORE;
	foreach($IGNORE as $idx=>$domain)
	if($domain == $d)
	return "";
	array_push($IGNORE, $d);
	$ccnt = Analysis::get_src_cnt($d);
	return preg_replace("/\/\/\/import\s+([\w\-\$]+(\.[\w\-\$]+)*);?/ies", "importSrc('\\1')", $ccnt['c']);
}
?>