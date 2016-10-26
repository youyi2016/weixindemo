<?php
/**
 * 微信公共接口测试
 *
 */
include("wechat.class.php");
class Test2
{
    public $options = array(
        'token' => 'weixin', //填写你设定的key
        //  'encodingaeskey'=>'encodingaeskey', //填写加密用的EncodingAESKey
        'appid' => 'wx6615a1d7a0d6489d', //填写高级调用功能的app id
        'appsecret' => '2d8bfa2c72f6ae8f58f090c56bf2cd7e', //填写高级调用功能的密钥
        'agentid' => '1', //应用的id
        'debug' => false,
        'logcallback' => 'logdebug'
    );

    public  function logdebug($text) {
        file_put_contents('../data/log.txt', $text . "\n", FILE_APPEND);
    }

    public function sendMsg($weObj) {
        //返回数组{'event'=>'','key'=>''}
        $event = $weObj->getRev()->getRevEvent();
        if($event[key]) {
            $this->dealEvent($weObj,$event[key]);
        }
        else {
            //点击菜单时type的MSGTYPE_EVENT有值
            $type = $weObj->getRev()->getRevType();
            $this->dealType($weObj,$type);
        }
    }

    //获取用户发送过来的消息类型
    public function dealType($weObj,$type)
    {
        switch ($type) {
            case Wechat::MSGTYPE_TEXT:
                //将消息发送给微信服务器
                $weObj->text("hello, I'm wechat,test1")->reply();
                exit;
                break;
            case Wechat::MSGTYPE_EVENT:
                $newsData = array(
                    "0"=>array(
                        'Title'=>'文体局',
                        'Description'=>'欢迎关注文体局！',
                        'PicUrl'=>'http://www.domain.com/1.jpg',
                        'Url'=>'http://www.domain.com/1.html'
                    ),
                );
                $weObj->news($newsData)->reply();
                // $weObj->text("hello, 欢迎关注我的测试号,test1！！")->reply();
                break;
            case Wechat::MSGTYPE_IMAGE:
                $mediaid = $weObj->getRevPic();
                $weObj->image($mediaid[mediaid])->reply();
                break;
            default:
                $weObj->text("help info")->reply();
                break;

        }
    }

    //菜单的事件推送
  public function dealEvent($weObj,$key) {
        switch($key) {
            case 'item1':
                $weObj->text("hello，welcome，click，用户服务")->reply();
                break;
            case 'item3':
                $weObj->text("hello，welcome，click,其他")->reply();
                break;
            default:
                break;
        }
    }


//我的测试号appid和appsecret 测试号的这俩个值是固定的
//获取access_token
    public function getAccessToken($weObj) {
        session_start();
        if($_SESSION['access_token'] && $_SESSION[ 'expire_time'] > time()) {
            return $_SESSION['access_token'];
        }
        else {
            $appid = 'wx6615a1d7a0d6489d';
            $appsecret= '2d8bfa2c72f6ae8f58f090c56bf2cd7e';
            //获取access_token 接口调用凭证
            $access_token = $weObj->checkAuth($appid,$appsecret);
            return $access_token;
        }
    }

//创建菜单
    public function creatMenus($weObj) {
        $data = array(
            'button' => array(
                array(//第一个一级菜单

                    'name'=>"用户服务",
                    'sub_button'=> array(
                        array(
                            'type'=>'view',
                            'name'=>'登录',
                            'url'=>'https://www.baidu.com'
                        ),
                        array(
                            'type'=>'view',
                            'name'=>'注册',
                            'url'=>'http://www.imooc.com'
                        ),
                        array(
                            'type'=>'click',
                            'name'=>'意见反馈',
                            'key' => 'item1'
                        ),
                        array(
                            'type'=>'view',
                            'name'=>'活动记录',
                            'url'=>'http://www.imooc.com'
                        ),
                    )
                ),
                array(//第二个一级菜单
                    'name'=>'文化活动',
                    'sub_button'=> array(//第一个二级菜单
                        array(
                            'type'=>'click',
                            'name'=>'主题',
                            'key' => 'item22'
                        ),
                        array(
                            'type'=>'view',
                            'name'=>'演出',
                            'url'=>'http://www.imooc.com'
                        ),
                        array(
                            'type'=>'view',
                            'name'=>'讲座',
                            'url'=>'http://www.imooc.com'
                        ),
                        array(
                            'type'=>'view',
                            'name'=>'展览',
                            'url'=>'http://www.imooc.com'
                        ),
                        array(
                            'type'=>'view',
                            'name'=>'培训',
                            'url'=>'http://www.imooc.com'
                        ),
                    )
                ),
                array(//第三个一级菜单
                    'name'=> '官网',
                    'type'=>'view',
                    'url'=> 'http://1.youyi2016.applinzi.com/WenTiJu/index2.html'
                    // 'sub_button'=> array(//第一个二级菜单
                    //    array(
                    //       'type'=>'view',
                    //       'name'=>'首页',
                    //       'url' => 'http://1.youyi2016.applinzi.com/20/index.html'
                    //   ),
                    //   array(
                    //      'type'=>'view',
                    //    'name'=>'书馆',
                    //       'url'=>'http://www.imooc.com'
                    //     ),
                    //  array(
                    //       'type'=>'view',
                    //     'name'=>'展馆',
                    //  'url'=>'http://www.imooc.com'
                    // ),
                    // array(
                    //     'type'=>'view',
                    //     'name'=>'演艺',
                    //    'url'=>'http://www.imooc.com'
                    // ),
                    // array(
                    //  'type'=>'view',
                    //   'name'=>'活动',
                    // 'url'=>'http://www.imooc.com'
                    // ),
                    //  )
                ),
            )
        );
        $access_token = $this->getAccessToken($weObj);
        //设置菜单的接口
        $url  = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
        //获取JsApi使用签名信息数组
        $weObj->getJsSign($url);
        //创建菜单
        $weObj->createMenu($data,$agentid='1');
    }
}

$mytest = new Test2();
$weObj = new Wechat($mytest->options);
//验证
$weObj->valid();
//给用户推送消息
$mytest->sendMsg($weObj);
//创建菜单
$mytest->creatMenus($weObj);

?>