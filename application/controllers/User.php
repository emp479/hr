<?php
/**
 * @name IndexController
 * @author tom
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class UserController extends Yaf_Controller_Abstract {

	/** 
     * 默认动作
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/hr/index/index/index/name/tom 的时候, 你就会发现不同
   */

  public function indexAction(){
    return $this->loginAction();
  }

  public function loginAction() {
    $submit = $this->getRequest()->getQuery("submit","0");
    if( $submit !="1" ){
      echo json_encode(array("errno"=>-1111,"errmsg"=>"请通过正常渠道登陆！"));
      return false;
    }

    $workid = $this->getRequest()->getPost("workid",false);
    $pwd = $this->getRequest()->getPost("pwd",false);
    if( !$workid ){
      echo json_encode(array("errno"=>"-3333","errmsg"=>"kkkkkk"));
      return false;
    }
    $userModel = new UserModel();
    $wid = $userModel->login(trim($workid),trim($pwd));
    if( $wid ){
      echo json_encode(
        array(
          "errno"=>0,
          "errmsg"=>"",
          "data"=>array(
            "name"=>$wid["user_work_id"],
            "pwd"=>$wid["user_password"],
            "auth"=>""
                  )
        )
      );
    }else{
      echo json_encode(
        array(
          "errno"=>$userModel->errno,
          "errmsg"=>$userModel->errmsg
        )
      );
    }
    return false;
  }

  public function registerAction(){
    $workid = $this->getRequest()->getPost("workid",false);
    $uname = $this->getRequest()->getPost("uname",false);
    $email = $this->getRequest()->getPost("email",false);
    $mobile = $this->getRequest()->getPost("mobile",false);
    $pwd = $this->getRequest()->getPost("pwd",false);

    if(!$workid || !$pwd || !$uname){
      echo json_encode(
        array(
          "errno" => -2121,
          "errmsg" => "员工编号、用户名或密码不能为空!"
        )
      );
      return false;
    }
    
    $data = array(
      "user_work_id" => trim($workid),
      "user_name" => trim($uname),
      "user_email" => trim($email),
      "user_mobile" => trim($mobile),
      "user_password" => trim($pwd),
      "user_id" => $this->_guid()
    );
    $table = "t_users";
    //$addUser = new UserModel();
    $userModel = new UserModel();
    $res = $userModel->register($data,$table);
    if($res){
      echo json_encode(
        array(
          "erron" => 0,
          "errmsg" =>"",
          "data" => array(
            "workid" =>$workid,
            "uname" => $uname
          )
        )
      );
    }else{
      echo json_encode(
        array(
          "errno"=>$userModel->errno,
          "errmsg"=>$userModel->errmsg
        )
      );
    }
    return false;
  }

 private function _guid(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12)
                .chr(125);// "}"
        return $uuid;
    }
}
}
