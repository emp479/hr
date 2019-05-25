<?php
/**
 * @name UserModel
 * @desc User数据获取类, 可以访问数据库，文件，其它系统等
 * @author tom
 */

class UserModel {
    public  $errno = 0;
    public  $errmsg = "";
    private static  $_db = null;

    public function __construct() {
      self::$_db = new PdoMySql; 
    }   
    
    public function login( $workid, $pwd) {
      $result = self::$_db->getAll("select user_work_id,user_password from t_users where user_work_id = ".$workid);
      if( !$result || count($result) != 1 ){
        $this->errno = "-1223333";
        $this->errmsg = "aaaaaaa";
        return false;
      }
      $userinfo = $result[0];
      if( $this->_password_generate( $pwd )!=$userinfo['user_password'] ){
        $this->errno = "-2222";
        $this->errmsg = "ffffff";
        return false;
      }

      //return intval($userinfo);
      return $userinfo;
    }

    public function register($data,$table){
      $result = self::$_db->getAll("select count(*) as id from t_users where user_work_id =".$data["user_work_id"]);
      if($result[0]["id"]!=0){
        $this->errno = "-12232";
        $this->errmsg = "user ext";
        return false;
      }
      if( strlen($data["user_password"]) <8){
        $this->errno = -12232222;
        $this->errmsg = "password len <8";
        return false;
      }else{
        $data["user_password"] = $this->_password_generate($data["user_password"]);
      }
      
      $ret = self::$_db->add($data,$table);
      
      if(!$ret){
        $this->errno = -1992;
        $this->errmsg = "asfdsafdsaf";
        return false;
      } 
      return true;
    }

    private function _password_generate( $password ){
      $pwd = md5("hrplat-gzmetro-".$password);
      return $pwd;
    }
}
