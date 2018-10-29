<?php
namespace app\index\controller;

use app\common\controller\Base;
use think\Db;
use app\common\model\Document;
use app\common\model\DocumentComments;

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

        //定义全局的查询变量
        $map = ['status' => 1];

        //接受用户搜索的关键字
        $keywords = input('param.keywords', '', 'trim');
        if($keywords != '')
        {
            $map['title'] = ['like', '%'.$keywords.'%'];
            $this->assign('page_header', '关键字：'.$keywords);
        }
        elseif($publish_date=input('param.publish_date'))
        {
            $this->assign('page_header','时间：'.$publish_date);
        }

        //展示当天的数据
        $publish_date = input('param.publish_date');
        if($publish_date)
        {
            $map['create_time'] = ['between', [strtotime($publish_date), strtotime($publish_date) + (60*60*24)]];
            $page_header = '时间：'.$publish_date;
        }

    	//基本的文章列表查询,删除$map = ['status'=>1];在顶端定义全局变量$map	
    	$lists = Db::name('document')->
        where($map)->
        order('create_time', 'desc')->
        paginate(config('PAGE_NUM_SET'));

    	$this->assign('_list',$lists);
    	$this->assign('title','首页');

        return $this->fetch();
    }

    //发布文章页面
    public function add()
    {
    	//用户未登录不能发布文章
    	$this->checkUserLogin();

    	//查询所有的文章分类列表
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
	    		'size' => 123456,
	    		'ext' => 'jpg,jpeg,png,gif',
	    	];
            
    		
			    // 移动到框架应用根目录/public/uploads/ 目录下
		      $info = $file->validate($map)->move(ROOT_PATH . 'public' . DS . 'uploads');


	        if($info){
	        	//上传成功保存在提交数据中去
	            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
	            $data['img_path'] = str_replace("\\", "/", $info->getSaveName());
	        }else{
	            // 上传失败获取错误信息
	            $this->error($file->getError());
	        }

	    	//3用户发布的文章进行数据库保存操作
	    	$docModel = new Document();
	    	if(!$docModel->allowField(true)->validate(true)->save($data))
	    	{
	    		$this->error($docModel->getError());
	    	}
	    	//4给用户提示和页面重定向（首页文章列表）
	    	$this->success('保存成功！',url('index/index/index'));
	    }
	    $this->error('参数错误！');
    }

    //显示文章详情页
    public function detail()
    {
        //获取id并检测参数是否正确
        $id = input('param.id');
        if(!$id)
        {
            $this->error('参数错误',url('index/index/index'));
        }

        //数据查询并验证
        $map = [
        'id' => $id,
        'status' => 1
        ];
        $info = Db::name('document')->where($map)->find();
        if($info === null)
        {
            $this->error('访问的文章不存在',url('index/index/index'));
        }

        $is_fav = 0;
        //判断用户是否已经收藏当前文章
        if(USER_ID)
        {
            $fav_map = [
                'uid' => USER_ID,
                'document_id' => $info['id']
            ];

            $fav_id = Db::name('document_fav')->where($fav_map)->value('id');
            if($fav_id)
            {
                $is_fav = 1;
            }          
        }
        $this->assign('is_fav', $is_fav);

        //每次pv+1
        Db::name('document')->where($map)->setInc('pv');



        $this->assign('info', $info);
        $this->assign('title', '详情页-'.$info['title']);
        return $this->fetch();
    }

    //收藏
    public function fav()
    {
        //请求判断
        if(!request()->isAjax())
        {
          return ['status'=>0, 'msg'=>'参数错误！'];  
        }

        //参数接受和验证
        $document_id = input('param.document_id');
        $uid = input('param.uid');
        if(!$document_id || !$uid)
        {  
            return ['status'=>0, 'msg'=>'参数错误或用户未登陆！'];
        }

        //执行收藏和取消收藏的操作
        $map = [
            'document_id' => $document_id,
            'uid' => $uid
        ];
        $type = 0;
        $fav_id = Db::name('document_fav')->where($map)->value('id');
        if($fav_id){
            //已经收藏过了，执行取消收藏操作
            Db::name('document_fav')->delete($fav_id);
            $type = 0;
        }
        else
        {   
            //未收藏过，进行收藏
            Db::name('document_fav')->insert(['document_id'=>$document_id,'uid'=>$uid]);
            $type = 1;
        }
        return ['status'=>1, 'msg'=>'操作成功！', 'type' => $type];
    }

    //发布评论
    public function add_commit()
    {
        $model = new DocumentComments();

        //5秒内不能重复发 cookie
        $c_name = 'doc_'.USER_ID.'_'.input('param.document_id');
        if(cookie($c_name))
        {
            if(time() - cookie($c_name) < 15)
            {
                return ['status'=>0, 'msg'=>'发布评论间隔时间太短！'];
            }
            else
            {
                cookie($c_name, time(), time()+60);
            }
        }
        else
        {
            cookie($c_name, time(), time()+60);
        }
        if($model->allowField(true)->validate(true)->save(input('post.')))
        {
            return ['status'=>1, 'msg'=>'发布评论成功！'];
        }
        else
        {
            return ['status'=>0, 'msg'=>$model->getError()];
        }
    }

    //ajax加载评论的列表
    public function getDocCommentList()
    {
        if(request()->isAjax())
        {

            $page_set = [
                'type'      => 'bootstrap',
                'var_page'  => 'page',
                'list_rows' => 5,
                'path' => url('detail', array('id'=>input('param.doc_id'))),
            ];

            $this->assign('comment_list',Db::name('document_comments')->
            where(['document_id'=>input('param.doc_id'),'status'=>1])->
            order('create_time','desc')->
             paginate($page_set) );
        }

        return $this->fetch();
    }



}
