<?php
/**
 * @name UserModel
 * @desc User数据获取类, 可以访问数据库，文件，其它系统等
 * @author tom
 */
class HealthModel {
    public  $errno = 0;
    public  $errmsg = "";
    private static  $_db = null;

    public function __construct() {
      self::$_db = new PdoMySql; 
    }   
    
    public function add( $data,$table) {
      $isEdit = false;
      
      if($data["health_staff_work_id"]!=0 && is_numeric($data["health_staff_word_id"])){
        $result = self::$_db->getAll("select * from t_health where health_staff_word_id = ".$data["health_staff_work_id"]);
        if(!$result || count($result) !=1){
          $this->errno = -535;
          $this->errmsg = "dfadsfdsafDS";
          return false;
        }
        $isEdit = true;
      }else{
        $isEdit = true;
        echo "fff";
        $res = self::$_db->getAll("select count(*) from t_health where health_staff_work_id = ".$data["health_staff_work_id"]);
        if(!$res /*|| $res[0][0] ==0*/){
          $this->errno = -345435;
          $this->errmsg = "dsfsdafdsa";
          return false;
        }
      }

     /* 
      $ret = self::$_db->add($data,$table);
      if(!$ret){
        $this->errno = -43543;
        $this->errmsg = "fdasfdsaf";
        return false;
      }
      */
      if($isEdit){
        echo "33333";
        $res = self::$_db->add($data,$table);//add
      }else{
        echo "pppp";
        unset($data["health_id"]);
        $res = self::$_db->update($data,$table,"health_staff_work_id =".$data["health_staff_work_id"],"health_staff_work_id desc","1");//update
      }
      if(!$res){
        $this->errno = -12222;
        $this->errmsg = "fsdafdsaF";
        return false;
      }

      if(!$isEdit){
        return intval(self::$_db->getLastInstallID());
      }else{
        return intval($data["health_staff_work_id"]);
      }
      //return true;
    }

    public function del($table,$where,$order,$limit){
      $res = self::$_db->delete($table,$where,$order,$limit);
      if(!$res){
        $this->errno =-34523;
        $this->errmsg = "sdfsdafdSA";
        return false;
      }
      return true;
    }

    public function status($workid,$status="offline"){
      return true;
    }

    public function get($workid){
      //$result = self::$_db->getAll("select * from t_health where health_staff_word_id = ".$data["health_staff_work_id"]);
      //if(){}
      return true;
    }

    public function list($pageNo,$pageSize=50,$cate=0,$status="online"){
      return true;
    }
}
