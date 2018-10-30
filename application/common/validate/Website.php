<?php
namespace app\common\validate;

use think\Validate;

/**
 * 系统设置验证类
 * Class Document
 */
class Website extends Validate
{
	//验证规则
	protected $rule = [
		'web_name' => ['require', 'length'=>'1,30'],
		'web_keywords' => ['require', 'length'=>'1,300'],
	];

}

