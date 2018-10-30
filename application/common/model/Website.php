<?php
namespace app\common\model;

use think\Model;

/**
 * 系统设置自定义模型
 * Class Document
 */
class Website extends Model
{	
	//表名
	protected $name = 'website';

	//自动写入时间戳
	protected $autoWriteTimestamp = true;

	//只是新增的时候
	protected $insert = [
		'status' => 1,
	];

	//只是更新的时候
	protected $update = [
		'update_time',
	];

}