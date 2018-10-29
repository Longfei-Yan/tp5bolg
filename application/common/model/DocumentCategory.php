<?php
namespace app\common\model;

use think\Model;

/**
* 
*/
class DocumentCategory extends Model
{
	
	protected $name = 'document_category';

	protected $autoWriteTimestamp = true;

	protected $insert = [
		'status' => 1
	];
}