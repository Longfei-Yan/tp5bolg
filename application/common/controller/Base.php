<?php
namespace app\common\controller;

use think\Controller;
use think\Request;

class Base extends Controller
{
	//初始化操作
	protected function _initialize()
	{
		parent::_initialize();

		//定义常量user_id
		define('USER_ID', session('user_id'));
	}

	//用户是否已经登录的检测
	public function checkUserLogin(){

		if(!USER_ID)
		{
			$this->error('用户未登录！禁止访问！',url('index/user/login'));
		}
	}

	public function checkLoginStatus(){

		if(USER_ID)
		{
			$this->error('用户已经登录！请勿重复登录！',url('index/user/index'));
		}
	}
}