<?php
/**
 * @name UserModel
 * @desc User数据获取类, 可以访问数据库，文件，其它系统等
 * @author tom
 */

require __DIR__.'/../../vendor/autoload.php';
use Nette\Mail\Message;

class MailModel {
    public  $errno = 0;
    public  $errmsg = "";
    private static  $_db = null;

    public function __construct() {
      self::$_db = new PdoMySql; 
    }   
    
    public function send( $workid, $title, $contents) {
      $result = self::$_db->getAll("select user_email from t_users where user_work_id = ".$workid);
      if( !$result || count($result) != 1 ){
        $this->errno = "-1223333";
        $this->errmsg = "aaaaaaa";
        return false;
      }
      $mailaddress = $result[0]["user_email"];
      if( !filter_var($mailaddress,FILTER_VALIDATE_EMAIL) ){
        $this->errno = "-2222";
        $this->errmsg = "ffffff";
        return false;
      }

      $mail = new Message;

      $mail->setFrom($mailaddress)
        ->addTo($mailaddress)
        ->setSubject($title)
        ->setBody($contents);
      
      $mailer = new Nette\Mail\SmtpMailer([
        'host' => 'smtp.yeah.net',
        'username' => 'chenxw1019@yeah.net',
        'password' => 'cxw831019',
        'secure' => 'ssl'
      ]);
      
      $rep = $mailer->send($mail);
      return true;
    }
}
