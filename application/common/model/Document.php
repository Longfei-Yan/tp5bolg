<?php
namespace app\common\model;

use think\Model;

/**
 * 文章自定义模型
 * Class Document
 */
class Document extends Model
{	
	//表名
	protected $name = 'document';

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