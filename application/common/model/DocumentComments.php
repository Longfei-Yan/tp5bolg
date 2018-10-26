<?php
namespace app\common\model;

use think\Model;

/**
* 
*/
class DocumentComments extends Model
{
	
	protected $name = 'document_comments';

	//自动写入时间戳
	protected $autoWriteTimestamp = true;


	//只是新增的时候
	protected $insert = [
		'status' => 1
	];

	//只是更新的时候
	protected $update = [
		'update_time'
	];
}