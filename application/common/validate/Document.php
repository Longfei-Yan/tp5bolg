<?php
namespace app\common\validate;

use think\Validate;

/**
 * 文章内容验证类
 * Class Document
 */
class Document extends Validate
{
	//验证规则
	protected $rule = [
		'title' => ['require','length'=>'1.30'],
		'document_category_id' => ['require'],
		'content' => ['require', 'length'=>'1,30000'],
		'uid' => ['require'],
	];

	//验证提示语
	protected $message = [
		'title.require' => '文章不能为空',
		'document_category_id.require' => '请选择文章分类',
		'content.require' => '文章内容不能为空',
		'uid.require' => '未登录无法发布文章'
	];
}

