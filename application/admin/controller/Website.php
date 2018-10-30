<?php
namespace app\admin\controller;

use app\common\controller\Base;
use app\common\model\Website as WebsiteSys;
use think\Db;

/**
 * 
 */
class Website extends Base
{
	//默认网站设置
	public function index()
	{
		$this->assign('info',Db::name('website')->find());
		$this->assign('show_type','website');
		$this->assign('title','系统设置-后台管理系统');
		return $this->fetch();
	}

	public function save()
	{
		if(request()->isPost())
		{
			$model = new WebsiteSys();
			
			if($model->allowField(true)
				->validate(true)
				->save(input('post.'), ['id'=>input('param.id')]))
			{
				$this->success('更新成功！');
			}
			$this->error($model->getError());

		}
		$this->error('参数错误！');
	}
}