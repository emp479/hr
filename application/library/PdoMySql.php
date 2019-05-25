<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 17-3-24
 * Time: 上午9:36
 */
class PdoMySql
{
    public static $config=array();//设置连接参数
    public static $link=null;//保存连接标识符
    public static $pconnect=false;//是否开启长连接
    public static $dbVersion=null;//保存数据库的版本号
    public static $connected=false;//是否连接成功
    public static $PDOStatement=null;//保存PDOStatement对象
    public static $queryStr=null;//保存最后执行的操作
    public static $error=null;//保存错误信息
    public static $lastInsertId=null;//保存上一步插入操作产生的AUTO_INCREMENT
    public static $numRows=0;//上一步操作产生影响的记录的条数

    /**
     * 连接PDO
     * PdoMySql constructor.
     * @param string $dbConfig
     * @return boolean
     */
    public  function  __construct($dbConfig='')
    {
        if(!class_exists("PDO")){
            self::throw_exception('不支持PDO，请先开启');
        }
        if(!is_array($dbConfig)){
            $dbConfig = array(
                'hostname'=>DB_HOST,
                'username'=>DB_USER,
                'password'=>DB_PWD,
                'database'=>DB_NAME,
                'hostport'=>DB_PORT,
                'dbms'=>DB_TYPE,
                'dns'=>DB_TYPE.":host=".DB_HOST.";dbname=".DB_NAME
            );
        }
        if(empty($dbConfig['hostname'])) self::throw_exception('没有定义数据库配置，请先配置');
        self::$config = $dbConfig;
        if(empty(self::$config['params']))self::$config['params']=array();
        if(!isset(self::$link)){
            $configs= self::$config;
            if(self::$pconnect){
                //开启长连接，添加到配置数组中
                $configs['params'][constant("PDO::ATTR_PERSISTENT")]=true;
            }
            try{
              self::$link=new \PDO($configs['dns'],$configs['username'],$configs['password'],$configs['params']);
            }catch (PDOException $e){
                self::throw_exception($e->getMessage());
            }
            if(!self::$link){
                self::throw_exception('PDO连接错误');
                return false;
            }
            self::$link->exec('SET NAMES '.DB_CHARSET);
            self::$dbVersion=self::$link->getAttribute(constant('PDO::ATTR_SERVER_VERSION'));
            self::$connected=true;
            unset($configs);
        }
    }

    /**
     * 得到结果集中的一条记录
     * @param string $sql
     * @return mixed
     */
    public static function getRow($sql=null){
        if($sql !=null){
            self::query($sql);
        }
        $result = self::$PDOStatement->fetch(constant('PDO::FETCH_ASSOC'));
        return $result;
    }

    /**
     * 解释字段
     * @param $fields
     * @return string
     */
    public static function parseFields($fields){
        if(is_array($fields)){
            array_walk($fields,array('PdoMySQL','addSpecilCher'));
            $fieldsStr = implode(',',$fields);
        }elseif (is_string($fields)&&!empty($fields)){
            if(strpos($fields,'`')===false){
                $fields=explode(',',$fields);
                array_walk($fields,array('PdoMySQL','addSpecilCher'));
                $fieldsStr=implode(',',$fields);
            }else{
                $fieldsStr=$fields;
            }
        }else{
            $fieldsStr='*';
        }
        return $fieldsStr;
    }

    /**
     * 通过反引号引用字段
     * @param $value
     * @return string
     */
    public static function addSpecilCher(&$value){
        if($value ==='*' || strpos($value,'.')!==false||strpos($value,'`')!==false){
            //不用做处理
        }elseif (strpos($value,'`')===false){
            $value='`'.trim($value).'`';
        }
        return $value;
    }
    /**
     * 执行增删改操作，返回受影响的记录的条数
     * @param string $sql
     * @return bool|unknown
     */
    public static function execute($sql=null){
        $link=self::$link;
        if(!$link) return false;
        self::$queryStr=$sql;
        //echo self::$queryStr;
        if(!empty(self::$PDOStatement))self::free();
        $result = $link->exec(self::$queryStr);
        self::haveErrorThrowException();
        if($result){
            self::$lastInsertId=$link->lastInsertId();
            self::$numRows=$result;
            return self::$numRows;
        }else{
            return false;
        }
    }

    /**
     * 根据主健查找记录
     * @param $tabName
     * @param $priId
     * @param string $fields
     * @return mixed
     */
    public static function findById($tabName,$priId,$fields='*'){
        $sql = 'SELECT %s FROM %s WHERE id=%d';
//        echo sprintf($sql,self::parseFields($fields),$tabName,$priId);
        return self::getRow(sprintf($sql,self::parseFields($fields),$tabName,$priId));
    }

    /**
     * 执行普通查询
     * @param $tables
     * @param null $where
     * @param string $fields
     * @param null $group
     * @param null $having
     * @param null $order
     * @param null $limit
     * @return unknown
     */
    public static function find($tables,$where=null,$fields='*',$group=null,$having=null,$order=null,$limit=null){
        $sql = 'SELECT '.self::parseFields($fields).' FROM '.$tables
            .self::parseWhere($where)
            .self::parsGroup($group)
            .self::parseHaving($having)
            .self::parseOrder($order)
            .self::parseLimit($limit);
//        echo $sql;
        $dataAll=self::getAll($sql);
//        if(count($dataAll)==1){
//            $rlt = $dataAll[0];
//        }else{
//            $rlt=$dataAll;
//        }
//        return $rlt;
//        var_dump(count($dataAll)==1?$dataAll[0]:$dataAll);
        return count($dataAll)==1?$dataAll[0]:$dataAll;
    }
    /**
    *得到数据库中的数据表
    */
    public static function showTables(){
        $tabls = array();
        if(self::query("SHOW TABLES")){
            $result = self::getAll();
            foreach ($result as $key => $val) {
                $tabls[$key] = current($val);
            }
        }
        return $tabls;
    }
    /**
    *获得数据库的版本号
    */
    public static function getDbVersion(){
        $link=self::$link;
        if(!$link) return false;
        return self::$dbVersion;
    }

    /**
    *得到上一步插入操作产生的AUTO_INCREMENT
    */
    public static function getLastInsertId(){
        $link=self::$link;
        if(!$link) return false;
        return self::$lastInsertId;
    }
    /**
    *得到最后执行的SQL语句
    */
    public static function getLastSql(){
        $link=self::$link;
        if(!$link) return false;
        return self::$queryStr;
    }
    public static function delete($table,$where,$order=null,$limit=0){
        $sql = "DELETE FROM {$table} "
        .self::parseWhere($where)
        .self::parseOrder($order)
        .self::parseLimit($limit);
        echo $sql;
        return self::execute($sql);
    }
    /**
    *更新记录
    */
    public static function update($data,$table,$where=null,$order=null,$limit=null){
        foreach ($data as $key => $val) {
            $sets .=$key."='".$val."',";
        }
        // echo $sets;
        $sets = trim($sets,',');
        $sql = "UPDATE {$table} SET {$sets} "
        .self::parseWhere($where)
        .self::parseOrder($order)
        .self::parseLimit($limit);
         echo $sql;
        return self::execute($sql);
    }
    /**
    *添加数据的操作
    */
    public static function add($data,$table){
        $keys = array_keys($data);
        array_walk($keys,array('PdoMySQL','addSpecilCher'));
        $fieldsStr = join(',',$keys);
        $values = "'".join("','",array_values($data))."'";
        $sql = "INSERT {$table} ({$fieldsStr}) VALUES({$values})";
       // echo $sql;
        return self::execute($sql);
    }
    /**
     * 解释限制显示条数limit
     * limit 3
     * limit 0,3
     * @param $limit
     * @return string
     */
    public static function parseLimit($limit){
        $limitStr='';
        if(is_array($limit)){
            if(count($limit)>1){
                $limitStr .=' LIMIT '.$limit[0].','.$limit[1];
            }else{
                $limitStr .=' LIMIT '.$limit[0];
            }
        }elseif (is_string($limit)&&!empty($limit)){
            $limitStr .=' LIMIT '.$limit;
        }
        return $limitStr;
    }

    /**
     * 解释order by
     * @param $order
     * @return string
     */
    public static function parseOrder($order){
        $orderStr='';
        if(is_array($order)){
            $orderStr.=' ORDER BY '.join(',',$order);
        }elseif (is_string($order)&&!empty($order)){
            $orderStr .=' ORDER BY '.$order;
        }
        return $orderStr;
    }
    /**
     * 对分组结果通过Having子句进行二次选
     * @param $having
     * @return string
     */
    public static function parseHaving($having){
        $havingStr='';
        if(is_string($having)&&!empty($having)){
            $havingStr .=' HAVING '.$having;
        }
        return $havingStr;
    }
    /**
     * 解释group by
     * @param $group
     * @return string
     */
    public  static function parsGroup($group){
        $groupStr='';
        if(is_array($group)){
            $groupStr .= ' GROUP BY '.implode(',',$group);
        }elseif(is_string($group)&&!empty($group)){
            $groupStr .= ' GROUP BY '.$group;
        }
        return empty($groupStr)?'':$groupStr;
    }
    /**
     * 解释WHERE条件
     * @param $where
     * @return string
     */
    public static function parseWhere($where){
        $whereStr='';
        if(is_string($where)&&!empty($where)){
            $whereStr=$where;
        }
        return empty($whereStr)?'':' WHERE '.$whereStr;
    }
    /**
     * 释放结果集
     */
    public static function free(){
        self::$PDOStatement=null;
    }

    public  static  function query($sql=''){
        $link=self::$link;
        if(!$link) return false;
        //判断之前是否有结果集，如果有释放之前的结果集
        if(!empty(self::$PDOStatement))self::free();
        self::$queryStr=$sql;
        self::$PDOStatement=$link->prepare(self::$queryStr);
        $res = self::$PDOStatement->execute();
        self::haveErrorThrowException();
        return $res;
    }
    public static function haveErrorThrowException(){
        $obj = empty(self::$PDOStatement)?self::$link:self::$PDOStatement;
        $arrError = $obj->errorInfo();
//        print_r($arrError);
        if($arrError[0] !='00000'){
            self::$error='SQLSTATE: '.$arrError[0].'SQL Error: '.$arrError[2].'<br/>Error SQL: '.self::$queryStr;
            self::throw_exception(self::$error);
            return false;
        }
        if(self::$queryStr ==''){
            self::throw_exception('没有执行的SQL语句');
            return false;
        }
    }

    /**
     * 得到所有记录
     * @param string $sql
     * @return unknown
     */
    public static function getAll($sql=null){
        if($sql !=null){
            self::query($sql);
        }
        echo $sql;
        $result = self::$PDOStatement->fetchAll(constant('PDO::FETCH_ASSOC'));
        return $result;
    }

    /**
     * 自定义错误处理
     * @param  unknown $errMsg
     */
    public static function throw_exception($errMsg){
        echo '<div style="width: 80%;background-color: #ABCDEF;color: black;font-size: 20px;">'.$errMsg.'</div>';
    }
    /**
    *销毁连接对象，关闭数据库
    */
    public static function close(){
        self::$link=null;
    }

}

#require '../../config.php';
#$pdoMySql = new PdoMySql;
//var_dump($PdoMySql);
#$sql = 'select * from t_users';
#print_r($pdoMySql->getAll($sql));
//$sql = 'select * from tassopenitem where assOpenItemId=8' ;
//print_r($PdoMySql->getRow($sql));
//echo "string";
//$sql = "INSERT tassopenitem(assOpenItemName,remarks) VALUES('96666','gggg')";
//echo $sql;
//$pdoMySql->execute($sql);
//echo $pdoMySql::$lastInsertId;
//echo 'aaaa';
//$sql ='delete from tassopenitem where id=15';
//$sql ="update  tassopenitem set assOpenItemName ='ffff' where id=16";
//var_dump($pdoMySql->execute($sql));
//$tabName='tassopenitem';
//$priId = '16';
// $fields = array('assOpenItemName'=>'44441','remarks'=>'ffff');
//$fields = '*';
//var_dump($pdoMySql->findById($tabName,$priId,$fields));
// $tables='tassopenitem';
//print_r($pdoMySql->find($tables));
//print_r($pdoMySql->find($tables,'id>=20'));
// print_r($pdoMySql->find($tables,'id>15','*','assOpenItemName','count(*)>=3'));
// $pdoMySql->add($fields,$tables);
// $pdoMySql->update($fields,$tables,'id<=19','id desc',array(5,10));
// var_dump($pdoMySql->delete($tables,'id>3','id desc','5'));
// var_dump($pdoMySql->showTables());
