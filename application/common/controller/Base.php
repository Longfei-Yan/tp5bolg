<?php
namespace app\common\controller;

use think\Controller;
use think\Request;
use think\Db;

class Base extends Controller
{
	//初始化操作
	protected function _initialize()
	{
		parent::_initialize();

		//定义常量user_id
		define('USER_ID', session('user_id'));

		//分类列表的变量置换
		$this->assign('menus', $this->showMenu());

		// 时间分组
		$this->getDocTimeGroupList();

		//当前用户选择的cid,变量置换到模板中
		$this->assign('category_id', input('param.category_id'), '');
	}

	//用户是否已经登录的检测
	public function checkUserLogin(){

		if(!USER_ID)
		{
			$this->error('用户未登录！禁止访问！',url('index/user/login'));
		}
	}

	//当用户已经登陆，无需重复登陆
	public function checkLoginStatus(){

		if(USER_ID)
		{
			$this->error('用户已经登录！请勿重复登录！',url('index/user/index'));
		}
	}

	// 显示分类
	public function showMenu(){
		$map = ['status' => 1];
    	return Db::name('document_category')
    	->where($map)
    	->order('sort', 'asc')
    	->select();
	}

	//获取最近七天文章发布的时间集合和当天发布的文章数量
	public function getDocTimeGroupList()
	{
		//最近七天
		$limit_time = time() - 7*24*60*60;
		$this->assign('doc_time_list', Db::name('document')
			->field("FROM_UNIXTIME(create_time,'%Y-%m-%d') as publish_date,COUNT(id) as doc_num")
			->where(['status'=>1, 'create_time'=>['egt',$limit_time]])
			->group('publish_date')
			->order('publish_date', 'desc')
			->select()
			);
	}
}