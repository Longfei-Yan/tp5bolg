<?php
namespace app\common\validate;

use think\Validate;

class User extends Validate
{
	protected $rule = [
		'username' => ['require','length'=>'5,18'],
		'email' => ['require','email'],
		'mobile' => ['require','number','unique'=>'user','length'=>11],
		'password' => ['require','confirm','regex'=>'/^[A-Za-z0-9\_]{6,8}$/'],
	];

	protected $message = [
		'username.require' => '用户名不能为空',
		'username.length' => '用户名长度在5到18位之间',
		'mobile.unique' => '此手机号已经被注册，请更换手机号',
		'password.regex' => '密码必须为6到18位的英文数字大小写和_符号',
	];
}