<?php
/**
 * @name IndexController
 * @author tom
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class MailController extends Yaf_Controller_Abstract {

	/** 
     * 默认动作
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/hr/index/index/index/name/tom 的时候, 你就会发现不同
   */

  public function indexAction(){
  }

  public function sendAction() {
    $submit = $this->getRequest()->getQuery("submit","0");
    if( $submit !="1" ){
      echo json_encode(array("errno"=>-1111,"errmsg"=>"请通过正常渠道登陆！"));
      return false;
    }

    $workid = $this->getRequest()->getPost("workid",false);
    $title = $this->getRequest()->getPost("title",false);
    $contents = $this->getRequest()->getPost("contents",false);

    if( !$workid || !$title || !$contents){
      echo json_encode(
        array(
          "errno" => -2324,
          "errmsg" => "dsajfkldsjafd"
        )
      );
      return false;
    }

    $sendModel = new MailModel();
    if($sendModel->send(intval($workid),trim($title),trim($contents))){
      echo json_encode(
        array(
          "errno" => 0,
          "errmsg" => ""
        )
      );
    }else{
      echo json_encode(
        array(
          "errno" => -34324,
          "errmsg" => "fasfdsa"
        )
      );
    }
    return false;
  }
}
