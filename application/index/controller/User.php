<?php
namespace app\index\controller;

use app\common\controller\Base;
use app\common\model\User as UserModel;

class User extends Base
{
		/**
	 * 用户注册展示界面
	 * @return [type] [description]
	 */
	public function reg()
	{
		$this->assign('title','用户注册');
		return $this->fetch();
	}
	/**
	 * ajax处理用户注册
	 */
	public function add()
	{
		if(request()->isAjax())
		{
			$user = new UserModel();
			if($user->allowField(true)->validate(true)->save(input('post.')))
			{
				return ['status' => 1,'msg' => '新用户注册成功！'];
			}
			else
			{
				return ['status' => 0,'msg' => $user->getError()];
			}
		}
		return ['status' => 0,'msg' => '未知错误'];
	}

	/**
	 * 用户登录界面
	 * @return [type] [description]
	 */
	public function login()
	{	
		$this->checkLoginStatus();
		$this->assign('title','用户登录');
		return $this->fetch();
	}

	/**
	 * 登录方法
	 * @return [type] [description]
	 */
	public function doLogin()
	{
		if(request()->isAjax())
		{
			$user = new UserModel();
			return $user->checkUserLogin(input('param.'));
		}
		return ['status' => 0];
	}

	/**
	 * 用户中心
	 * @return [type] [description]
	 */
	public function index()
	{	
		$this->checkUserLogin();
		return $this->fetch();
	}

	/**
	 * 注销登录
	 * @return [type] [description]
	 */
	public function loguot()
	{
		session('user_id',null);
		session('user_info',null);
		$this->success('注销成功',url('index/user/login'));
	}


}