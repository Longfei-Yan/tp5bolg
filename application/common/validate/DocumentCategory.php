<?php
namespace app\common\validate;

use think\Validate;

/**
 * 文章内容验证类
 * Class Document
 */
class DocumentCategory extends Validate
{
	//验证规则
	protected $rule = [
		'name' => ['require', 'length'=>'1,300','unique'=>'document_category'],
		'uid' => ['require'],
	];

	//验证提示语
	protected $message = [
		'uid.unique' => '分类已经存在'
	];
}

