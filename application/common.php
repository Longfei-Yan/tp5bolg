<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Db;
// 应用公共文件

//匹配status的各种状态
if(!function_exists('getStatusTitle'))
{
	function getStatusTitle($id)
	{
		$list = config('STATUS_LIST');
		return $list['$id'];
	}
}

//根据id获取用户的姓名
if(!function_exists('getUserName'))
{
	function getUserName($id)
	{
		return Db::name('user')->where(['id'=>$id])->value('username');
	}
}

//
if(!function_exists('getDocBody'))
{
	function getDocBody($content)
	{
		return mb_substr(strip_tags($content), 0 , 500).'......';
	}
}

//getCategoryName
if(!function_exists('getCategoryName'))
{
	function getCategoryName($id)
	{
		return db('document_category')->where('id='.$id)->value('name');
	}
}