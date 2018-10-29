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

		//初始化一些常量
		define('CONTROLLER_NAME',Request::instance()->controller());
		define('MODULE_NAME',Request::instance()->module());
		define('ACTION_NAME',Request::instance()->action());

		//检测已经登录的用户是否拥有后台访问的权限
		$this->checkAdminUserAuth();
	}

	//获取列表的方法
	public function lists($model_name = '', $map = [], $order = 'create_time desc')
	{
		if($model_name && !empty($map))
		{
			return Db::name($model_name)
			->where($map)
			->paginate(config('paginate'));
		}

		return false;
	}

	//修改状态的方法
	public function setStatus($model_name,$id=0,$status=0)
	{
		if($model_name)
		{
			$map['id'] = $id;
			$data['status'] = $status;
			if(Db::name($model_name)->where($map)->update($data))
			{

				return $this->success('修改状态成功！');
			}
		}

		return $this->error('修改状态失败！');


	}

	//如果是管理后台，则判断当前用户是否有权限
	private function checkAdminUserAuth()
	{
		if(MODULE_NAME == 'admin')
		{
			//无需验证控制器User
			if(CONTROLLER_NAME == 'User')
			{
				return true;
			}

			//用户登录验证
			if(!USER_ID)
			{	
				$this->redirect(url('admin/user/login'));
			}

			//用户权限验证
			if(session('user_info.is_admin') != 1)
			{
				session('user_id', NULL);
				session('user_info', NULL);
				$this->error('没有后台管理的权限！',url('user/login'));
			}
		}
		return true;
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