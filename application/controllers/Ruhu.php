<?php
/**
 * @name RuhuController
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
  
  }
  /*
  *添加入户信息
  *
  */
  public function addAction($workid = 0){
  	if(!$this->_isAdmin()){
      echo json_encode(
        array(
          "errno" => -2353,
          "errmsg" => "请确认你的身份是否有权限!"
        )
      );
      return false;
    }
    
    $submit = $this->getRequest()->getQuery("submit","0");
    if( $submit !="1" ){
      echo json_encode(array("errno"=>-1111,"errmsg"=>"请通过正常渠道登陆！"));
      return false;
    }
    
    $table = "t_ruhu";
    $data = array(
    	
      "health_id" => "dfds".rand(100000,999999),//id号
      
      "health_staff_work_id" => trim($this->getRequest()->getPost("workid",false)),//员工编号
      
      "health_staff_name" => trim($this->getRequest()->getPost("name",false)),//姓名
      //性别
      "health_staff_organization_code" => trim($this->getRequest()->getPost("orgcode",false)),
      //出生日期
      "health_staff_post" => trim($this->getRequest()->getPost("post",false)),
      //部门
      "health_staff_rank" => trim($this->getRequest()->getPost("rank",false)),
      //入司日期
      "health_staff_sex" => trim($this->getRequest()->getPost("sex",false)),
      //
      //"health_staff_age" => trim($this->getRequest()->getPost("age",false)),
      "health_staff_marital_status" => trim($this->getRequest()->getPost("status",false)),
      "health_staff_IDcardNo" => trim($this->getRequest()->getPost("cno",false)),
      "health_staff_phone_number" => trim($this->getRequest()->getPost("mobile",false)),
      "health_staff_inspect_datetime" => trim($this->getRequest()->getPost("inspectdt",false)),
      "health_staff_due_datetime" => trim($this->getRequest()->getPost("duedt",false)),
      "health_enclosure" => trim($this->getRequest()->getPost("enc",false)),
      "health_ramark" => trim($this->getRequest()->getPost("ram",false))
    );
  }
  
  public function delAction(){
  
  }
  
  public function editAction(){
  
  }
  
  public function listAction(){
  
  }
  
  public function findAction(){
  
  }
}
