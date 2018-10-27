<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Privilege extends Model
{
    protected $table = 'privilege';

    public function findAll()
    {
        $data = Db::name('privilege')->select();
        return $this->_tree($data);
    }

    public function _tree($data,$parent_id = 0,$level = 0)
    {
        static $arr = [];
        foreach($data as $v)
        {
            if($v['parent_id'] == $parent_id)
            {
                $v['level'] = $level;
                $arr[] = $v;
                $this->_tree($data,$v['id'],$level+1);
            }
        }
        return $arr;
    }
}