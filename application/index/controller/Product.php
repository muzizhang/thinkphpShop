<?php
namespace app\index\controller;

use think\Request;
use think\facade\Env;

class Product extends Base
{
    //  产品列表页
    public function getindex()
    {
        return view('product/product/index');
    }

    //  添加产品
    public function getPinsert()
    {
        //  取出一级分类
        $cat1_id = \Db::table('category')->where('parent_id',0)->select();
        //  取出品牌
        $brand = \Db::table('brand')->field('id,brand_name')->select();
        return view('product/product/insert',[
            'cat1_id'=>$cat1_id,
            'brand'=>$brand
        ]);
    }

    //  处理添加产品
    public function postPAdd()
    {
        // echo '<pre>';
        // var_dump($_POST);
        $goodsData = [
            'goods_name'=>$_POST['goods_name'],
            'goods_desc'=>$_POST['goods_desc'],
            'is_sale'=>$_POST['is_sale'],
            'brand_id'=>$_POST['brand_id'],
            'category_id'=>'-'.$_POST['cat1_id'].'-'.$_POST['cat2_id'].'-'.$_POST['cat3_id'].'-'
        ];
        $goodsId = \Db::table('goods')->insertGetId($goodsData);
        //    更新spu表
        $spuData = ['goods_id'=>$goodsId];
        $spuId = \Db::table('goods_spu')->insertGetId($spuData);
        //    添加sku属性
        $key = explode(',',$_POST['goodsAttrKey']);
        $value = explode(',',$_POST['goodsAttrValue']);
        $length = explode(',',$_POST['goodsAttrLength']);
        //  对其数组进行截取
        foreach($length as $k=>$v)
        {
            $sum = 0;
            for($i=0;$i<$k;$i++)
            {
                $sum += $length[$i];
            }
            $attrValue[] = array_slice($value,$sum,$v);
        }
        // echo "key<br>";
        // var_dump($key);
        // echo "value<br>";
        // var_dump($value);
        foreach($key as $k=>$v)
        {
            $attrData = [
                'sku_name'=>$v,
                'spu_id'=>$spuId
            ];
            $attrkey[$k][] = \Db::table('goods_skuattr')->insertGetId($attrData);
            array_push($attrkey[$k],$v);
        }
        foreach($attrValue as $k=>$v)
        {
            foreach($v as $k1=>$v1)
            {
                $valueData = ['sku_value'=>$v1,'attr_id'=>$attrkey[$k][0]];
                $skuValue[$k][$k1][] = \Db::table('goods_skuvalue')->insertGetId($valueData);
                array_push($skuValue[$k][$k1],$v1);
            }
        }
        // echo "attrkey</br>";
        // var_dump($attrkey);
        // echo "skuvalue</br>";
        // var_dump($skuValue);
        //   将其根据|分割
        $attr_key = [];
        $attr_value = [];
        foreach($_POST['attr_name'] as $k=>$v){
            $data = explode("|",$v);
            $attr_key = explode(":",$data[0]);
            $attr_value[] = explode(":",$data[1]);
        }
        // echo "attr_key<br>";
        // var_dump($attr_key);
        // echo "attr_value<br>";
        // var_dump($attr_value);
        foreach($_POST['attr_name'] as $k=>$v)   //012
        {
            $path = [];
            foreach($skuValue as $k1=>$v1)     //  01
            {
                foreach($v1 as $k2=>$v2)   //  01
                {
                    // var_dump($v2[1].'-----'.$k2);
                    // var_dump($attr_value[$k][$k1]);

                    if($v2[1] == $attr_value[$k][$k1])
                    {
                        // var_dump('23');
                        $path[] = $attrkey[$k1][0].':'.$v2[0];
                    }
                }
            }
            //  将数组转成字符串
            $path = implode('|',$path);
            \Db::table('goods_sku')
                ->data([
                    'sku_path'=>$path,
                    'stock'=>$_POST['stock'][$k],
                    'price'=>$_POST['price'][$k]
                ])
                ->insert();
        }
        
        //  根据隐藏域传递的路径取出文件
        $oldFile = Env::get('root_path').'public'.$_POST['image'];
        $newFile = Env::get('root_path').'public/static/upload/goods/'.date('Ymd').'/';
        if(!is_dir(Env::get('root_path').'public/static/upload/goods/'.date('Ymd')))
        {
            mkdir(Env::get('root_path').'public/static/upload/goods/'.date('Ymd'),0777,true);
        }
        foreach(scandir($oldFile) as $k=>$v)
        {
            if($k>1)
            {
                if(copy($oldFile.'/'.$v,$newFile.$v))
                {
                    // 82*82    142*142   220*282
                    //   打开图像文件进行操作
                    $image = \think\Image::open($newFile.$v);
                    $image->thumb(82,82,\think\Image::THUMB_CENTER)->save($newFile.'sm_'.$v);
                    $image->thumb(142,142,\think\Image::THUMB_CENTER)->save($newFile.'md_'.$v);
                    $image->thumb(220,282,\think\Image::THUMB_CENTER)->save($newFile.'big_'.$v);
                    unlink($oldFile.'/'.$v);
                    \Db::table('goods_image')
                        ->data([
                            'spu_id'=>$spuId,
                            'path'=>'/static/upload/goods/'.date('Ymd').'/'.$v,
                            'big_path'=>'/static/upload/goods/'.date('Ymd').'/big_'.$v,
                            'md_path'=>'/static/upload/goods/'.date('Ymd').'/md_'.$v,
                            'xs_path'=>'/static/upload/goods/'.date('Ymd').'/sm_'.$v,
                        ])
                        ->insert();
                }
            }
        }
        $this->redirect('/product/index');
    }

    public function postImage()
    {
        $files = request()->file('file');
        if(!is_dir(Env::get('root_path').'public/static/tmp/'.session('id')))
        {
            mkdir(Env::get('root_path').'public/static/tmp/'.session('id'),0777,true);
        }
        $info = $files->rule('uniqid')->move(Env::get('root_path').'public/static/tmp/'.session('id'));
        if($info)
        {
            $info->getSaveName();
        }
        echo json_encode([
            'path'=>'/static/tmp/'.session('id'),
        ]);
    }

    //  分类列表
    public function getCategory()
    {   
        //  取出所有分类
        $data = \Db::table('category')
            ->field("*,concat(path,id,'-') pathId")
            ->where(1,1);
        if(isset($_GET['name']))
        {
            $data = $data->where('cate_name','like','%'.$_GET['name'].'%');
        }

        $data = $data->order('id,pathId')->select();
            
        return view('product/category/index',[
            'data'=>$data,
        ]);
    }

    //  获取所有的分类
    public function getCategoryAll()
    {
        $data = \Db::table('category')
                    ->field("*,concat(path,id,'-') pathId")
                    ->order('id,pathId')
                    ->select();
        echo json_encode([
            'data'=>$data
        ]);
    }

    //  添加分类
    public function getCategoryInsert()
    {
        //  取出一级分类
        $cat1_id = \Db::table('category')->where('parent_id',0)->select();
        return view('product/category/insert',[
            'cat1_id'=>$cat1_id,
        ]);
    }

    //  获取二，三级分类
    public function getCat2_id()
    {
        $data = \Db::table('category')
                    ->where('parent_id',$_GET['id'])
                    ->select();
        echo json_encode([
            'data'=>$data
        ]);
    }

    //  处理添加
    public function postCategoryAdd()
    {
        if(!isset($_POST['cat1_id']))
        {
            \Db::table('category')
                ->data([
                    'cate_name'=>$_POST['category_name']
                ])
                ->insert();
        }
        else if(!isset($_POST['cat2_id']))
        {
            \Db::table('category')
            ->data([
                'cate_name'=>$_POST['category_name'],
                'parent_id'=>$_POST['cat1_id'],
                'path'=>'-'.$_POST['cat1_id'].'-'
            ])
            ->insert();
        }
        else
        {
            \Db::table('category')
                ->data([
                    'cate_name'=>$_POST['category_name'],
                    'parent_id'=>$_POST['cat2_id'],
                    'path'=>'-'.$_POST['cat1_id'].'-'.$_POST['cat2_id'].'-'
                ])
                ->insert();
        }
        
        $this->redirect('/product/Category');
    }

    //   编辑分类
    public function getCategoryEdit()
    {
        //  取出所有分类
        $cat1_id = \Db::table('category')
                        ->field("*,concat(path,id,'-') pathId")
                        ->select();
        //  获取出当id的数据
        $data = \Db::table('category')->where('id',$_GET['id'])->find();
        return view('product/category/edit',[
            'cat1_id'=>$cat1_id,
            'data'=>$data
        ]);
    }

    //  处理编辑
    public function postCategoryUpdate()
    {
        \Db::table('category')
            ->where('id',$_GET['id'])
            ->data(['cate_name'=>$_POST['category_name']])
            ->update();
        $this->redirect('/product/Category');
    }

    //  删除分类
    public function getCategoryDelete()
    {
        \Db::table('category')
            ->where('id',$_GET['id'])
            ->delete();
        $this->redirect('/product/Category');
    }

    //  品牌列表
    public function getBrand()
    {
        //  取出所有的品牌信息
        $data = \Db::table('brand')
                    ->where(1,1);
        if(isset($_GET['name']))
        {
            $data = $data->where('brand_name','like','%'.$_GET['name'].'%')
                        ->whereor('description','like','%'.$_GET['name'].'%');
        }
        if(isset($_GET['start']))
        {
            $data = $data->where('created_at','>',$_GET['start']);
        }
        if(isset($_GET['type']))
        {
            $data = $data->where('brand_type',$_GET['type']);
        }
        $data = $data->paginate(1,false,['query'=>request()->param()]);
        $page = $data->render();

        //   取出国内产品和国外产品的数量/  总数
        $count = \Db::table('brand')->field('count(*) count')->find();
        $inCount = \Db::table('brand')->field('count(*) count')->where('brand_type','1')->find(); 
        $outCount = \Db::table('brand')->field('count(*) count')->where('brand_type','0')->find(); 
        return view('product/brand/index',[
            'data'=>$data,
            'page'=>$page,
            'count'=>$count,
            'inCount'=>$inCount,
            'outCount'=>$outCount,
        ]);
    }

    //  添加品牌
    public function getBrandInsert()
    {
        $req = new Request();
        $token = $req->token('__token__','sha1');
        return view('product/brand/insert',[
            'token'=>$token
        ]);
    }

    //  处理品牌添加
    public function postBrandAdd()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->validate(['size'=>5242880,'ext'=>'jpg,png,gif,bmp,jpeg,webp,tif,pcx'])
                    ->move(Env::get('root_path').'public/static/upload');
        if($info){
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            //  上传成功，将其保存到数据库中
            \Db::table('brand')
                ->data([
                    'brand_name'=>$_POST['brand_name'],
                    'brand_LOGO'=>'/upload/'.$info->getSaveName(),
                    'brand_type'=>$_POST['type'],
                    'link'=>$_POST['brand_link'],
                    'description'=>$_POST['brand_desc']
                ])
                ->insert();

            $this->redirect('/product/Brand');
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }

    //   编辑品牌
    public function getBrandEdit()
    {
        //    取出当前品牌
        $data = \Db::table('brand')
                    ->where('id',$_GET['id'])
                    ->find();
        return view('product/brand/edit',[
            'data'=>$data
        ]);
    }

    //  处理编辑
    public function postBrandUpdate()
    {
        //  删除原图片
        $img = \Db::table('brand')
                    ->where('id',$_GET['id'])
                    ->field('brand_LOGO')
                    ->find();
        unlink(Env::get('root_path').'/public/static'.$img['brand_LOGO']);
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->validate(['size'=>5242880,'ext'=>'jpg,png,gif,bmp,jpeg,webp,tif,pcx'])
                    ->move(Env::get('root_path').'public/static/upload');
        if($info){
            //  上传成功，将其保存到数据库中
            \Db::name('brand')
                ->where('id',$_GET['id'])
                ->data([
                    'brand_name'=>$_POST['brand_name'],
                    'brand_LOGO'=>'/upload/'.$info->getSaveName(),
                    'brand_type'=>$_POST['type'],
                    'link'=>$_POST['brand_link'],
                    'description'=>$_POST['brand_desc']
                ])
                ->update();

            $this->redirect('/product/Brand');
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }

    //  删除品牌
    public function getBrandDelete()
    {
        //  删除图片
        $img = \Db::table('brand')
                    ->where('id',$_GET['id'])
                    ->field('brand_LOGO')
                    ->find();
        unlink(Env::get('root_path').'/public/static'.$img['brand_LOGO']);
        //  删除数据库中的数据
        \Db::table('brand')->where('id',$_GET['id'])->delete();
        $this->redirect('/product/Brand');
    }
}