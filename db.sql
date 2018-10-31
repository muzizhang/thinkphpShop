`用户表`
字段
    id，用户名，密码，手机号，邮箱，
`用户详情表`
字段
    id，用户id，性别，昵称(注册成功，随机生成昵称)，职业id，头像，生日(年，月，日)，用户所在地，会员等级，默认收货地址，消费金额（eg:100块钱，会员等级为1）

**订单完成，更新会员等级，消费金额，，，，，算法**  
`收货地址表`
字段
    id，地址，path_id(城市path)，用户id，地址状态，联系电话，收件人姓名
`用户详情表和用户所在地中间表`
字段
    用户id，所在地id
`城市表`
    id，地域名称，上级id，path 1-2-3
    
`职业表`
字段
    id，职业名称
`银行卡`
字段
    id，银行卡号，持有者，银行名称，用户id

`配送表`
字段
    id，订单编号，快递单号，快递公司，发件人，发件人邮编，发件人手机，发件人详细地址，发件人地址[地址path]，收件人，收件人手机号,收件人地址[地址path]，收件人详细地址，收件人邮编，物流状态[api返回值：已揽收，在途中，已签收，问题件]，更新时间，物流订单创建时间，创建时间

`订单中心`
字段
    订单id，订单状态[未支付，已支付，已取消，待发货，已发货，未评价，退款中，已退款，订单成功]，sku_id，商品名称，商品信息，商品数量，订单编号，收货人，交易时间，快递公司，快递单号，地址，手机号码，付款方式，spu
`退款记录表`
字段
    id，订单id，订单编号，收款人，退款方，退款金额，退款时间，退款方式
    
`购物车`
字段
    id，商品名称，商品信息，单价，数量，商品编号，市场价，购买价，sku_id，用户id，spu，属性
`收藏表`
字段
    id，用户ID，sku_id，商品信息，商品名称，

`访问记录`
字段
    id，用户id，商品名称，sku_id ，商品信息，商品分类

`商品信息表`
字段
    id，商品名称，商品描述，商品属性_id，spu_id，是否上架，品牌id，分类id，浏览量

`商品分类表`
字段
    id，分类名称，上级id，path

drop table if exists category;
create table category
(
    id int unsigned not null auto_increment comment 'ID',
    cate_name VARCHAR(255) not null comment '分类名称',
    parent_id int unsigned not null DEFAULT 0 comment '上级id',
    path VARCHAR(255) not null DEFAULT '-' comment 'path',
    primary key (id)
)engine='InnoDB' comment='商品分类表';

INSERT INTO category(id,cate_name,parent_id,path) VALUES
    (1,'家用电器',0,'-'),
        (2,'电视',1,'-1-'),
            (3,'曲面电视',2,'-1-2-'),
            (4,'超薄电视',2,'-1-2-'),
            (5,'OLED电视',2,'-1-2-'),
            (6,'4k超清电视',2,'-1-2-'),
        (7,'冰箱',1,'-1-'),
            (8,'多门',7,'-1-7-'),
            (9,'对开门',7,'-1-7-'),
            (10,'酒柜',7,'-1-7-'),
            (11,'双门',7,'-1-7-'),
        (12,'空调',1,'-1-'),
            (13,'柜式空调',12,'-1-12-'),
            (14,'中央空调',12,'-1-12-'),
            (15,'变频空调',12,'-1-12-'),
            (16,'壁挂式空调',12,'-1-12-'),
        (17,'洗衣机',1,'-1-'),
            (18,'滚筒洗衣机',17,'-1-17-'),
            (19,'洗烘一体机',17,'-1-17-'),
            (20,'波轮洗衣机',17,'-1-17-'),
            (21,'迷你洗衣机',17,'-1-17-'),
    (22,'手机',0,'-'),
        (23,'手机通讯',22,'-22-'),
            (24,'手机',23,'-22-23-'),
            (25,'游戏手机',23,'-22-23-'),
            (26,'老人机',23,'-22-23-'),
            (27,'对讲机',23,'-22-23-'),
        (28,'手机配件',22,'-22-'),
            (29,'手机壳',28,'-22-28-'),
            (30,'贴膜',28,'-22-28-'),
            (31,'移动电源',28,'-22-28-'),
            (32,'数据线',28,'-22-28-');

`品牌表`
字段
    id，品牌名称，品牌LOGO，链接，描述

drop table if exists brand;
create table brand
(
    id int unsigned not null auto_increment comment 'ID',
    brand_name VARCHAR(255) not null comment '品牌名称',
    brand_LOGO VARCHAR(255) not null comment '品牌LOGO',
    link VARCHAR(255) not null comment '链接',
    description VARCHAR(255) not null comment '描述',
    PRIMARY key (id)
)engine='InnoDB' comment='品牌表';

INSERT INTO brand(id,brand_name,brand_LOGO,link,description) VALUES
    (1,)

`商品属性表`
字段
    id，属性名称，属性值，spu_id

`商品图片表`
字段
    id，spu_id，path（OSS），图片描述，big_path，md_path，xs_path

`商品sku属性表`
字段
    id，sku名称，spu_id
    1    颜色      1
    2    内存      1

****`商品sku中间表`
字段
    id，sku_path，库存量，价格  
    1     1:1|2:3

`商品sku属性值`
字段
    id，sku值，attr_id
    1    白色     1
    2    黑色     1 
    3    4G       2
    4    64G      2


`管理员登录表`
字段
    id，管理员id，管理员名称，登录时间，登录ip，登录地点，内容

drop table if exists admin_login;
create table admin_login
(
    id int unsigned not null auto_increment comment 'ID',
    admin_id int unsigned not null comment '管理员id',
    admin_name VARCHAR(255) not null comment '管理员名称',
    content VARCHAR(255) not null default "登录成功" comment "登录状态",
    login_ip VARCHAR(255) not null comment '登录ip',
    login_address VARCHAR(255) not null comment '登录地点',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP not null comment '登录时间',
    primary key (id),
    key admin_id(admin_id) 
)engine="InnoDB" comment='后台管理员登录记录表';

`后台日志表`
记录后台用户的行为记录，创建时间
    id，管理员id，管理员名称，管理员角色，操作行为[操作的id(添加/修改/删除商品的id)，操作的内容]，创建时间，行为类型id，行为path，

drop table if exists admin_logs;
create table admin_logs
(
    id int unsigned not null auto_increment comment "ID",
    admin_id int unsigned not null comment '管理员id',
    admin_name VARCHAR(255) not null comment '管理员名称',
    admin_role VARCHAR(255) not null comment '管理员角色',
    behavior_type_id int unsigned not null comment '行为类型id',
    behavior_type_path VARCHAR(255) not null comment '行为类型path',
    practive VARCHAR(255) not null comment '操作行为[操作id，操作内容]',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP not null comment '创建时间',
    primary key (id),
    key admin_id(admin_id),
    key behavior_type_id(behavior_type_id)
)engine='InnoDB' comment="后台日志表";

-- `后台行为类型表`
-- 字段
--     id，类型名称，parent_id，path       pri_id
--     1    产品       0         -           1
--     2    商品       1         -1-         6
--     3    添加商品   2         -1-2-       7

-- drop table if exists admin_behavior_type;
-- create table admin_behavior_type
-- (
--     id int unsigned not null auto_increment comment 'ID',
--     type_name VARCHAR(255) not null comment '类型名称',
--     parent_id int unsigned not null DEFAULT '0' comment '上级id',
--     path VARCHAR(255) not null DEFAULT '-' comment 'path',
--     pri_id int unsigned not null comment '权限id',
--     primary key (id)
-- )engine='InnoDB' comment='后台行为类型表';

-- INSERT INTO admin_behavior_type(id,type_name,parent_id,path,pri_id) VALUES
--     (1,'产品模块',0,'-',1),
--         (2,'商品',1,'-1-',6),
--             (3,'添加商品',2,'-1-2-',7),
--             (4,'修改商品',2,'-1-2-',10),
--             (5,'删除商品',2,'-1-2-',11);


`权限表`
字段
    id，权限名称，上级id，path,url

drop table if exists privilege;
create table privilege
(
    id int unsigned not null auto_increment comment 'ID',
    pri_name varchar(255) not null comment '权限名称',
    parent_id int unsigned not null default '0' comment '上级id',
    url VARCHAR(255) not null default '' comment 'url路径',
    path VARCHAR(255) not null DEFAULT '-' comment 'path',
    primary key (id)
)engine='InnoDB' comment='权限表';

INSERT INTO privilege(id,pri_name,parent_id,url,path) VALUES
(1,'产品模块',0,'','-'),
    (2,'分类列表',1,'product/category','-1-'),
        (3,'添加分类',2,'product/CategoryInsert,product/CategoryAdd','-1-2-'),
    (4,'品牌列表',1,'product/Brand','-1-'),
        (5,'添加品牌',4,'product/BrandInsert,product/BrandAdd','-1-4-'),
    (6,'商品列表',1,'product/index','-1-'),
        (7,'添加商品',6,'product/Pinsert,product/Padd','-1-6-'),
-- (14,'管理员模块','',0),
--     (15,'权限列表','privilege/index',14),
--         (16,'添加权限',15,'privilege/create,privilege/insert',15),
--         (17,'修改权限',15,'privilege/edit,privilege/update',15),
--         (18,'删除权限',15,'privilege/delete',15),
--     (19,'角色列表','role/index',14),
--         (20,'添加角色',19,'role/create,role/insert',19),
--         (21,'修改角色',19,'role/edit,role/update',19),
--         (22,'删除角色',19,'role/delete',19),
--     (23,'管理员列表','admin/index',14),
--         (24,'添加管理员',23,'admin/create,admin/insert',23),
--         (25,'修改管理员',23,'admin/edit,admin/update',23),
--         (26,'删除管理员',23,'admin/delete',23);



`角色权限表`
字段
    权限id，角色id
drop table if exists privilege_role;
create table privilege_role
(
    pri_id int unsigned not null comment '权限id',
    role_id int unsigned not null comment '角色id',
    key pri_id(pri_id),
    key role_id(role_id)
)engine='InnoDB' comment='权限角色表';

`角色表`
字段
    id，角色名称

drop table if exists role;
create table role
(
    id int unsigned not null auto_increment comment 'ID',
    role_name VARCHAR(255) not null comment '角色名称',
    primary key (id)
)engine='InnoDB' comment='角色表';


`角色管理表`
字段
    角色id，管理员id

drop table if exists role_admin;
create table role_admin
(
    role_id int unsigned not null comment '角色id',
    admin_id int unsigned not null comment '管理员id',
    key role_id(role_id),
    key admin_id(admin_id)
)engine='InnoDB' comment='角色管理表';

`管理员表`
字段
    id，名称，密码

drop table if exists admin;
create table admin
(
    id int unsigned not null auto_increment comment 'ID',
    name VARCHAR(255) not null comment '名称',
    password VARCHAR(255) not null comment '密码',
    primary key (id)
)engine='InnoDB' comment='管理员表';

`文章表`
字段
    id，标题，内容[富文本]，管理员id，分类path，分类id，是否显示

`文章分类表`
字段
    id，分类名称，上级id，path

`评论表`
字段
    id，spu_id，用户id，sku_id，内容，评价等级，点赞数，回复数

`回复表`
字段
    评论id，回复内容

`广告表`
字段
    id，区域[显示位置]，类型，内容，链接，图片，标题，点击数，订单数，创建时间，失效时间，状态[0 失效  1 创建]，

`优惠券`
字段
    id，金额，最低消费，创建时间，失效时间，发放数量，spu_id[无门槛]，可用时间范围[12:00-14:00/18:00-20:00]，用户等级数领取，
`用户优惠券表`
    优惠券id，用户id，优惠券状态[待使用，已使用，已过期]




获取ip信息
http://api.map.baidu.com/location/ip?ip=118.75.163.0&ak=639GKD6xyO3u82qD0aDxV2Rmdo9nVaaH
http://ip.taobao.com/service/getIpInfo.php?ip=118.75.163.0
http://apis.juhe.cn/ip/ip2addr?ip=36.23.225.26&key=63b18b9d95e59ee7dccc9e6476aaeb7b
http://api.ip138.com/query/?ip=8.8.8.8&datatype=jsonp&callback=find&token=4c3ea19d8bc48bdbccba23300fad2140
~~~PHP

银行卡
https://www.juhe.cn/docs/api/id/221
<?php
// +----------------------------------------------------------------------
// | JuhePHP [ NO ZUO NO DIE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2015 http://juhe.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: Juhedata <info@juhe.cn>
// +----------------------------------------------------------------------
 
//----------------------------------
// 银行卡实名认证调用示例代码 － 聚合数据
// 在线接口文档：https://www.juhe.cn/docs/api/id/188
//----------------------------------
 
header('Content-type:text/html;charset=utf-8');
 
 
//配置您申请的appkey
$appkey = "*********************";
 
 
 
 
//************1.银行卡实名认证查询************
$url = "http://v.juhe.cn/verifybankcard/query";
$params = array(
      "bankcard" => "",//银行卡卡号
      "realname" => "",//姓名(需utf8编码的urlencode)
      "key" => $appkey,//应用APPKEY(应用详细页查询)
);
$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
$result = json_decode($content,true);
if($result){
    if($result['error_code']=='0'){
        print_r($result);
    }else{
        echo $result['error_code'].":".$result['reason'];
    }
}else{
    echo "请求失败";
}
//**************************************************
 
 
 
 
 
/**
 * 请求接口返回内容
 * @param  string $url [请求的URL地址]
 * @param  string $params [请求的参数]
 * @param  int $ipost [是否采用POST形式]
 * @return  string
 */
function juhecurl($url,$params=false,$ispost=0){
    $httpInfo = array();
    $ch = curl_init();
 
    curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
    curl_setopt( $ch, CURLOPT_USERAGENT , 'JuheData' );
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 60 );
    curl_setopt( $ch, CURLOPT_TIMEOUT , 60);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if( $ispost )
    {
        curl_setopt( $ch , CURLOPT_POST , true );
        curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
        curl_setopt( $ch , CURLOPT_URL , $url );
    }
    else
    {
        if($params){
            curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
        }else{
            curl_setopt( $ch , CURLOPT_URL , $url);
        }
    }
    $response = curl_exec( $ch );
    if ($response === FALSE) {
        //echo "cURL Error: " . curl_error($ch);
        return false;
    }
    $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
    $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
    curl_close( $ch );
    return $response;
}
~~~