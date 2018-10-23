<?php
namespace app\index\controller;

use app\common\controller\Base;
use think\Db;
use app\common\model\Document;

class Index extends Base
{	
	//首页
    public function index()
    {	
    	//分类检索
    	$category_id = input('param.category_id', '');
    	if($category_id != '')
    	{
    		$map['document_category_id'] = $category_id;
    		$page_header = '分类名称：'.getCategoryName($category_id);
    	}


    	$this->assign('title','首页');
        return $this->fetch();
    }

    //发布文章页面
    public function add()
    {
    	$this->checkUserLogin();

    	//用户未登录不能发布文章
    	$map = ['status' => 1];
    	$catesLists = Db::name('document_category')->where($map)->select();

    	//变量置换
    	$this->assign('catesLists',$catesLists);
    	$this->assign('title','文章发布');

   		return $this->fetch();
    }

    //保存文章
    public function save()
    {
    	//实现文章的发布（带图片上传）
    	if(request()->isPost())
    	{
	    	//1数据获取和数据验证
	    	$data = input('post.');

	    	//2文件信息的获取验证和上传保存
	    	$file = request()->file('img_path');

	    	//文件上传格式的限定
	    	$map = [
	    		'size' => '123456',
	    		'ext' => 'jpg,jpeg,png,gif',
	    	];
    		if($file){
			    // 移动到框架应用根目录/public/uploads/ 目录下
		        $info = $file->validate($map)->move(ROOT_PATH . 'public' . DS . 'uploads');

		        if($info){
		        	//上传成功保存在提交数据中去
		            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
		            $data['img_path'] = $info->getSaveName();

		        }else{
		            // 上传失败获取错误信息
		            $this->error($file->getError());
		        }

		    	//3用户发布的文章进行数据库保存操作
		    	$docModel = new Document();
		    	if($docModel->allowField(true)->validate(true)->save($data))
		    	{
		    		$this->error($docModel->getError());
		    	}
		    	//4给用户提示和页面重定向（首页文章列表）
		    	$this->success('保存成功！',url('index/index/index'));
		    }
		    else
		    {
		    	$this->error('上传的文件格式错误！');
		    }
	    }
	    $this->error('参数错误！');
    }


}
