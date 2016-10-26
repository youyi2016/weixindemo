<?php
//1.TOKEN用于服务器配置 验证安全性
define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapiTest();

//echostr随机数已经初始化就验证
if (isset($_GET['echostr'])) {
    //2.验证消息来自微信服务器
    $wechatObj->valid();
    // $wechatObj->definedItems();

}else{
    $wechatObj->responseMsg();
}

class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];
         //若确认此次GET请求来自微信服务器，则原样返回echostr参数内容，接入生效，成为开发者成功，否则接入失败。
        if($this->checkSignature()){
            header('content-type:text');
            echo $echoStr;
            exit;
        }
    }

    
    //检验signature
    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        //将token、timestamp、nonce三个参数进行字典序排序
        sort($tmpArr, SORT_STRING);
        //将三个参数字符串拼接成一个字符串进行sha1加密
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        //开发者获得加密后的字符串与signature对比，标识该请求来源于微信
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
    
    //3.依据接口文档实现业务逻辑
    public function responseMsg()
    {
        //获取微信推送过来的post数据（xml数据）
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        if (!empty($postStr)){
            //simplexml_load_string() 函数把XML字符串载入对象中。
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $picUrl = $postObj->PicUrl;
            $msgId = $postObj->MsgId;
            $mediaId = $postObj->MediaId;
            $event = strtolower($postObj->MsgType);
            $eventkey = strtolower($postObj->EventKey);
            $msgType = "text";
            $msgType2 = "image"; 
            $msgType3 = "music";
            $msgType4 = "news";
            $time = time();
            //返回给用户文本类型数据
            $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <Event><![CDATA[subscribe]]></Event>
                        <FuncFlag>0</FuncFlag>
                        </xml>";
            //返回给用户图片类型数据
            $textTp2 = "<xml>                     
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Image>
                        <MediaId><![CDATA[%s]]></MediaId>
                        </Image>
                        </xml> ";
            
             //返回给用户发送音乐
            $arrMusic = array(
                array(
                    'title'=>'约定',
                    'description'=>'陈奕迅',
                    'musicurl'=>'http://odnc53sha.bkt.clouddn.com/yueding.mp3',
                    'hqmusicurl'=>'http://odnc53sha.bkt.clouddn.com/yueding.mp3'
                ),
                array(
                    'title'=>'沙龙',
                    'description'=>'陈奕迅',
                    'musicurl'=>'http://odnc53sha.bkt.clouddn.com/shalong.mp3',
                    'hqmusicurl'=>'http://odnc53sha.bkt.clouddn.com/shalong.mp3'
                ),
            );
           
            $textTp3 ="<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Music>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <MusicUrl><![CDATA[%s]]></MusicUrl>
                        <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                        </Music>
                       <FuncFlag>0</FuncFlag>
                        </xml> ";
            
            
            //发送图文消息
            $arr = array(
                array(
                   'title'=>'工商13级精英班',
                    'description'=>'武汉工商学院13级精英班组建于2015年10月,它是武汉工商学院对13级计算机专业设定的第一个创新性班级,该班级学员由通过选拔之后的武汉工商学院13级计科专业的18名学生组成',
                    'picurl'=>'http://odnc53sha.bkt.clouddn.com/jy.jpg',
                    'url'=>'http://www.imooc.com',
                ),           
                
            );
           
            $textTp4 = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <ArticleCount>".count($arr)."</ArticleCount>
                        <Articles>";
            
            foreach($arr as $k=>$v) {
           
                           $textTp4 .= "<item>
                                <Title><![CDATA[".$v['title']."]]></Title>
                                <Description><![CDATA[".$v['description']."]]></Description>
                                <PicUrl><![CDATA[".$v['picurl']."]]></PicUrl>
                                <Url><![CDATA[".$v['url']."]]></Url>
                                </item>";
                      
             }
            
             $textTp4 .= " </Articles>   
                           </xml>";
            
            
            $arr2 = array(
                array(
                   'title'=>'2016年3月新闻发布系统项目答辩',
                    'description'=>'2015年寒假新闻发布系统项目答辩',
                    'picurl'=>'http://odnc53sha.bkt.clouddn.com/jinying.jpg',
                    'url'=>'http://www.imooc.com',
                ),
                array(
                   'title'=>'13级精英班2016年1月第一次聚餐',
                    'description'=>'',
                    'picurl'=>'http://odnc53sha.bkt.clouddn.com/20163.png',
                    'url'=>'http://www.imooc.com',
                ),
                  array(
                   'title'=>'2016年3月第一批同学出校实习第二次聚餐',
                    'description'=>'',
                    'picurl'=>'http://odnc53sha.bkt.clouddn.com/20160319.jpg',
                    'url'=>'http://www.imooc.com',
                ),
                 
                
                
            );
           
            $textTp5 .= "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <ArticleCount>".count($arr2)."</ArticleCount>
                        <Articles>";
            
            foreach($arr2 as $k=>$v) {
           
                           $textTp5 .= "<item>
                                <Title><![CDATA[".$v['title']."]]></Title>
                                <Description><![CDATA[".$v['description']."]]></Description>
                                <PicUrl><![CDATA[".$v['picurl']."]]></PicUrl>
                                <Url><![CDATA[".$v['url']."]]></Url>
                                </item>";
                      
             }
             $textTp5 .= " </Articles>   
                           </xml>";
            
            
            //事件推送，粉丝关注时推送的消息
            if($event == "event") {
                 $contentStr = "欢迎关注,此公众号是开发模式,现在ni可以发送图片和文字来测试我的公众号;新增歌曲：约定、沙龙,回复歌曲名可以听到歌曲;有些回复我还会秒回哦！试试你的手气吧！举个栗子：回复女神或多高,嘿嘿~";
                 $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                 echo $resultStr;
            }
            //粉丝发送的文本信息      
            if($event == "text") {
                 if($keyword == "约定"|| $keyword == "沙龙")
                 {
                     foreach($arrMusic as $k=>$v) {
                         if($v['title'] == $keyword) {
                             //    $resultStr2 = sprintf($textTp3, $fromUsername, $toUsername, $time, $msgType3);
                             $resultStr2 = sprintf($textTp3, $fromUsername, $toUsername, $time, $msgType3, $v['title'], $v['description'], $v['musicurl'], $v['hqmusicurl']);
                            echo $resultStr2; 
                             break;
                         }
                     }
                  
                 } 
                 if($keyword == "精英班")
                 {     
                     $this->responseTuWen($textTp4, $fromUsername, $toUsername, $time, $msgType4, $keyword); 
                  
                 } 
                if($keyword == "精英班日常") 
                {
                    $this->responseTuWen($textTp5, $fromUsername, $toUsername, $time, $msgType4, $keyword); 
                  
                }
                
                else 
                {
                $this->responseText($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr, $keyword);
                }
            }      
              //发送和粉丝一样的图片
             if($event == "image") {
                 $this->responseImage($textTp2, $fromUsername, $toUsername, $time, $msgType2, $mediaId);
                 
             }
            
            if( strtolower($postObj->Event) == 'click') {
                if ($eventkey == 'item1') {
                   $contentStr = "item1";
                     $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                     echo $resultStr;
                }
            }


        }else{
            echo "";
            exit;
        }
    }
    
    //给粉丝发送文本信息
    public function responseText($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr,$keyword) {
         
        switch($keyword) {
            case '三围' :
            $contentStr ="我木有三围,你有吗？";
            break;
            
            case '帅哥' :
            $contentStr ="我是萌妹子,好伐";
            break;
            
            case '多高':
            $contentStr = "比你高~";
            break;
            
            case '女神':
            $contentStr = "你也长的很好看啊~";
            break;
            
            case 'i love you':
            $contentStr = "I love you too 么么~";
            break;
            
            case '这是什么':
            $contentStr = "这是我的个人公众号啊~和其他公众号类似";
            break;
            
            case '?' :
            $contentStr = '送你一串时间:'.date("Y-m-d H:i:s",time());  
            break;
            
            default :
            $contentStr = $keyword;
            break;
            
        }
        
             $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
             echo $resultStr;
           
    }
    
    //给粉丝发送图片信息
    public function responseImage($textTp2, $fromUsername, $toUsername, $time, $msgType2, $mediaId) {
         $resultStr2 = sprintf($textTp2, $fromUsername, $toUsername, $time, $msgType2, $mediaId);
         echo $resultStr2;
    }
    
    //给粉丝发送图文消息
    public function responseTuWen($textTp4, $fromUsername, $toUsername, $time, $msgType4, $keyword) {
       
         $resultStr2 = sprintf($textTp4, $fromUsername, $toUsername, $time, $msgType4, $keyword);
         echo $resultStr2;
    }
    
//返回access_token
    public function getAccessToken() {
        //access_token存在session中则返回
        // if($_SESSION['access_token'] && $_SESSION[ 'expire_time'] > time())
        //{
        //return $_SESSION['access_token'];
        //} 
        //access_token不存在session中则重新获取
        //else {
        //$appid = 'wx6615a1d7a0d6489d';
        // $secret= '2d8bfa2c72f6ae8f58f090c56bf2cd7e';
        $appid = 'wxdd5b028c5a441c48';
        $secret = '4bd23b5b79a9272be4bbd97375d26f28';
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
        $res = $this->http_curl($url, 'post', 'json');  
        $access_token = $res['access_token'];
        $_SESSION['access_token'] = $access_token;
        $_SESSION['expire_time'] = time()+7000;
         return $access_token;
        //}
    }
    
    //php中进行get和post请求
      function http_curl($url, $type='get', $res='json',$arr='') {
        //初始化curl
        $ch = curl_init();
        //设置curl参数  下面的方式是get请求
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在 
    
        //post请求
        if ($type == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在 
        }
        //采集curl
        $output = curl_exec($ch);
        //关闭
        curl_close($ch);
        if($res='json') {
            //请求失败返回错误信息
             if( curl_errno($ch) ) {
               return curl_error($ch);
           }//返回成功
            else {
            //加上参数true 将json对象转化成数组而不仅仅是object类型
            return curl_decode($output,true);
           }
        }
        var_dump($output);  
    }
    
    //自定义菜单
  public  function definedItems() {
		header('content-type:text/html;charset=utf-8');
	     $access_token = $this->getAccessToken();
        $url  = " https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
        $postArr = array(
           'button' => array(
           array(//第一个一级菜单
                'type'=>'click',
                'name'=>"全民导航",
                'key' => 'item1'
            ),
            array(//第二个一级菜单
               'name'=>'在线', 
                'sub_button'=> array(//第一个二级菜单
				array(
                     'type'=>'click',
                    'name'=>'在线2',
                    'key' => 'item22'
                ),
				)
            ),
            array(//第三个一级菜单
                 'type'=>'click',
                'name'=> 'menu3',
                'key' => 'item3'
            ),
          )
        );
     echo $postJson = json_encode($postArr);
       $res = $this->http_curl($url,'post','json',$postJson);
        var_dump($res);
        
    }

    
}


?>