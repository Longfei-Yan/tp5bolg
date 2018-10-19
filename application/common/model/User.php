<?php
namespace app\common\model;

use think\Model;

class User extends Model
{
	protected $name = 'user';

	//自动写入时间戳
	protected $autoWriteTimestamp = true;

	//自定完成 新增和更新都执行
	protected $auto = [
		'password',
	];

	//只是新增的时候
	protected $insert = [
		'status' => 1,
	];

	//只是更新的时候
	protected $update = [
		'update_time',
	];

	// 密码md5序列化
	public function setPasswordAttr($value,$data)
	{
		if($data['password'])
		{	
			//未知问题，未使用md5加密则未加密，使用md5一次则出现加密两次的结果
			return md5($data['password']);
		}
		return '';
	}

	public function checkUserLogin($data)
	{
		//构建查询的条件
		$map = [
			'mobile' => $data['mobile'],
			'password' => md5(md5($data['password'])),
			'status' => 1
		];

		$userinfo = self::get($map);

		if($userinfo === null)
		{
			return ['status' => 0, 'msg' => '手机号或密码错误！'];
		}

		if($userinfo['status'] < 1)
		{
			//todo return msg
		}

		//记录用户的状态
		session('user_id' , $userinfo->data['id']);
		session('user_info' , $userinfo->data);

		return ['status' => 1, 'msg' => '用户登录成功！正常跳转...'];
	}
}