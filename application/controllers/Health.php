<?php
/**
 * @name IndexController
 * @author tom
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class HealthController extends Yaf_Controller_Abstract {

	/** 
     * 默认动作
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/hr/index/index/index/name/tom 的时候, 你就会发现不同
   */

  public function indexAction(){
    return $this->listAction();
  }

  public function addAction($workid =0) {
    if(!$this->_isAdmin()){
      echo json_encode(
        array(
          "errno" => -2353,
          "errmsg" => "dsafdsaf"
        )
      );
      return false;
    }

    $submit = $this->getRequest()->getQuery("submit","0");
    if( $submit !="1" ){
      echo json_encode(array("errno"=>-1111,"errmsg"=>"请通过正常渠道登陆！"));
      return false;
    }
    
    $table = "t_health";
    $data = array(
      "health_id" => "dfds".rand(100000,999999),
      "health_staff_work_id" => trim($this->getRequest()->getPost("workid",false)),
      "health_staff_name" => trim($this->getRequest()->getPost("name",false)),
      "health_staff_organization_code" => trim($this->getRequest()->getPost("orgcode",false)),
      "health_staff_post" => trim($this->getRequest()->getPost("post",false)),
      "health_staff_rank" => trim($this->getRequest()->getPost("rank",false)),
      "health_staff_sex" => trim($this->getRequest()->getPost("sex",false)),
      //"health_staff_age" => trim($this->getRequest()->getPost("age",false)),
      "health_staff_marital_status" => trim($this->getRequest()->getPost("status",false)),
      "health_staff_IDcardNo" => trim($this->getRequest()->getPost("cno",false)),
      "health_staff_phone_number" => trim($this->getRequest()->getPost("mobile",false)),
      "health_staff_inspect_datetime" => trim($this->getRequest()->getPost("inspectdt",false)),
      "health_staff_due_datetime" => trim($this->getRequest()->getPost("duedt",false)),
      "health_enclosure" => trim($this->getRequest()->getPost("enc",false)),
      "health_ramark" => trim($this->getRequest()->getPost("ram",false))
    );

    if( !$data["health_staff_work_id"] || !$data["health_staff_name"] || !$data["health_staff_IDcardNo"] 
      || !$data["health_staff_phone_number"] ){
      echo json_encode(
        array(
          "errno" => -2324,
          "errmsg" => "dsajfkldsjafd"
        )
      );
      return false;
    }
    
    $model = new HealthModel();
    
    if($lastworkid = $model->add($data,$table)){
      echo json_encode(
        array(
          "errno" => 0,
          "errmsg" => "",
          "data" => array(
            "lastwordid" => $data["health_staff_work_id"],
            "lastwordname" => $data["health_staff_name"]
          )
        )
      );
    }else{
      echo json_encode(
        array(
          "errno" => $model->errno,
          "errmsg" => $model->errmsg
        )
      );
    }
    return false;
  }

  public function editAction(){
    if(!$this->_isAdmin()){
      echo json_encode(
        array(
          "errno" =>-325,
          "errmsg" => "dsfdsafds"
        )
      );
      return false;
    }

    $workid = $this->getRequest()->getQuery("workid","0");
    if(is_numeric($workid) && $workid){
      return $this->addAction($workid);
    }else{
      echo json_encode(
        array(
          "errno" => -235,
          "errmsg" => "dsafdsaf"
        )
      );
    }
    return false;
  }

  public function delAction(){
    if(!$this->_isAdmin()){
      echo json_encode(
        array(
          "errno" => -23532,
          "errmsg" => "fdsafdas"
        )
      );
      return false;
    }
    $workid = $this->getRequest()->getQuery("workid","0");
    $table = "t_health";
    if(is_numeric($workid) && $workid){
      $model = new HealthModel();
      if($model->del($table,"health_staff_work_id =".$workid,"health_staff_work_id desc","5")){
        echo json_encode(
          array(
            "errno" => 0,
            "errmsg" => ""
          )
        );
      }else{
        echo json_encode(
          array(
            "errno" => -34634,
            "errmsg" => "dfsdafdsaf"
          )
        );
      }
    }else{
      echo json_encode(
        array(
          "errno" => $model->errno,
          "errmsg" => $model->errmsg
        )
      );
    }
    return false;
  }

  public function statusAction(){
    if(!$this->_isAdmin()){
      echo json_encode(
        array(
          "errno" => -2525,
          "errmsg" => "dsafdsaf"
        )
      );
      return false;
    }

    $workid = $this->getRequest()->getQuery("workid","0");
    $status = $this->getRequest()->getQuery("status","offline");

    if(is_numeric($workid) && $workid){
      $model = new HealthModel();
      if($model->status($workid,$status)){
        echo json_encode(
          array(
            "errno" => 0,
            "errmsg" =>""
          )
        );
      }else{
        echo json_encode(
          array(
            "errno" => $model->errno,
            "errmsg" => $model->errmsg
          )
        );
      }
    }else{
      echo json_encode(
        array(
          "errno" =>-32532,
          "errmsg" => "dfdsaf"
        )
      );
    }
    return true;
  }

  public function getAction(){
    $workid = $this->getRequest()->getQuery("workid","0");

    if(is_numeric($workid) && $workid){
      $model = new HealthModel();
      if($data = $model->get($workid)){
        echo json_encode(
          array(
            "errno" => 0,
            "errmsg" => "",
            "data" => $data
          )
        );
      }else{
        echo json_encode(
          array(
            "errno" => -323423,
            "errmsg" => "dfsadfdsa"
          )
        );
      }
    }else{
      echo json_encode(
        array(
          "errno" => -436436,
          "errmsg" => "dsfdsafdsa"
        )
      );
    }
    return true;
  }

  public function listAction(){
    $pageNo = $this->getRequest()->getQuery("pageNo","0");
    $pageSize = $this->getRequest()->getQuery("pageSize","50");
    $cate = $this->getRequest()->getQuery("cate","0");
    $status = $this->getRequest()->getQuery("status","online");

    $model = new HealthModel();
    if($data = $model->list($pageNo,$pageSize,$cate,$status)){
      echo json_encode(
        array(
          "errno" => 0,
          "errmsg" =>"",
          "data" => $data
        )
      );
    }else{
      echo json_encode(
        array(
          "errno" => -32543,
          "errmsg" => "dfasfdsaf"
        )
      );
    }
    return true;
  }

  private function _isadmin(){
    return true;
  }
}
