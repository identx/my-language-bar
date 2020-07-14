<?php //дб
require_once("config.php");

abstract class simpleDB{
	protected $db_resource;
	protected $returnMethod;
	public $queryInfo;
	protected $stmp;
	public function select($query){
		$this->returnMethod='mysqliFetchAssoc';
		$arguments=func_get_args();
		return call_user_func_array(array($this,'s_query'),$arguments);
    }
    public function selectCol($query){
		$this->returnMethod='mysqliFetchCol';
		$arguments=func_get_args();
		return call_user_func_array(array($this,'s_query'),$arguments);
	}
	public function selectCell($query){
		$this->returnMethod='mysqliFetchCell';
		$arguments=func_get_args();
		return call_user_func_array(array($this,'s_query'),$arguments);
	}
	public function selectRow($query){
		$this->returnMethod='mysqliFetchRow';
		$arguments=func_get_args();
		return call_user_func_array(array($this,'s_query'),$arguments);
	}
	public function update($query){
		$arguments=func_get_args();
		return call_user_func_array(array($this,'i_query'),$arguments);
	}
    public function insert($query){
		$arguments=func_get_args();
		return call_user_func_array(array($this,'i_query'),$arguments);
	}
	public function replace($query){
		$arguments=func_get_args();
		return call_user_func_array(array($this,'i_query'),$arguments);
	}
    public function delete($query){
		$arguments=func_get_args();
		return call_user_func_array(array($this,'i_query'),$arguments);
	}
    public function other_query($query){
		$arguments=func_get_args();
		return call_user_func_array(array($this,'i_query'),$arguments);
	}
}

class simpleMysqli extends simpleDB{
	public function __construct($connectionSettingsArray){
		extract($connectionSettingsArray);
		$this->db_resource=new mysqli($server,$username,$password,$db,$port);
		$this->db_resource->set_charset($charset);
		if($this->db_resource->connect_error)
			throw new Exception($this->db_resource->connect_error);
	}
	public function transactionStart(){
		$this->db_resource->autocommit(false);
	}
	public function transactionCommit(){
		$this->db_resource->commit();
		$this->db_resource->autocommit(true);
	}
	public function transactionRollBack(){
		$this->db_resource->rollback();
	}
    protected function s_query(){
        $data=null;
        $arguments=func_get_args();
        $this->query($arguments);
        if(!$this->stmp)
			return false;
        $execute=$this->stmp->execute();
        if(!$execute)
			return false;
        $result=$this->bindResult($data);
        if(!$result)
			return false;
        $returnPrepareMethod=$this->returnMethod;
        $rows=$this->$returnPrepareMethod($data);
        $this->setQueryInfo();
        return $rows;
    }
	public function simpleQuery($query){
        return $this->db_resource->query($query);
    }
	protected function query($arguments){
		$this->prepareQuery($arguments);
		$query=$arguments[0];
		if($this->stmp)
			$this->stmp->close();
		$this->stmp=$this->db_resource->prepare($query);
		if(!$this->stmp)
			return false;
		if(count($arguments)>1){
			$bindVars=$arguments;
			unset($bindVars[0]);
			$params=array();
			$binding=$this->bindParams($bindVars,$params);
			if(!$binding)
				return false;
		}
		return true;
	}
	protected function i_query(){
		$arguments=func_get_args();
		$this->query($arguments);
		if(!$this->stmp)
			return false;
		$execute=$this->stmp->execute();
		if(!$execute)
			return false;
		$this->setQueryInfo();
		return true;
	}
    protected function setQueryInfo(){
		$info=array(
			'affected_rows'=>$this->stmp->affected_rows,
			'insert_id'=>$this->stmp->insert_id,
			'num_rows'=>$this->stmp->num_rows,
			'field_count'=>$this->stmp->field_count,
			'sqlstate'=>$this->stmp->sqlstate,
		);
		$this->queryInfo=$info;
	}
    public function error(){
		return $this->db_resource->error;
	}
    public function errno(){
		return $this->db_resource->errno;
	}
    protected function prepareQuery(&$arguments){
		$sprintfArg=array();
		$sprintfArg[]=$arguments[0];
		foreach($arguments as $pos=>$var){
			if(is_array($var)){
				$insertAfterPosition=$pos;
				$replaceWith=array();
				unset($arguments[$pos]);
				foreach($var as $arrayVar){
					array_splice($arguments,$insertAfterPosition,0,$arrayVar);
					$insertAfterPosition++;
					$replaceWith[]='?';
				}
				$sprintfArg[]=implode(',',$replaceWith);
			}
		}
		// $arguments[0]=call_user_func_array('sprintf',$sprintfArg);
	}
	private function bindParams($bindVars,&$params){
		$params[]=$this->getParamTypes($bindVars);
        foreach($bindVars as $key=>$param)
			$params[]=&$bindVars[$key];
        return call_user_func_array(array($this->stmp,'bind_param'),$params);
	}
	private function bindResult(&$data){
		$this->stmp->store_result();
		$variables=array();
		$meta=$this->stmp->result_metadata();
		while($field=$meta->fetch_field())
			$variables[]=&$data[$field->name];
		return call_user_func_array(array($this->stmp,'bind_result'),$variables);
	}
	private function mysqliFetchAssoc(&$data){
		$i=0;
		$array=array();
		while($this->stmp->fetch()){
			$array[$i]=array();
			foreach($data as $k=>$v)
				$array[$i][$k]=$v;
			$i++;
		}
		return $array;
	}
	private function mysqliFetchCol(&$data){
		$i=0;
		$array=array();
		while($this->stmp->fetch()){
			$array[$i]=array();
			foreach($data as $v){
				$array[$i]=$v;
				break;
			}
			$i++;
		}
		return $array;
	}
    private function mysqliFetchRow(&$data){
		$this->stmp->fetch();
		return $data;
	}
    private function mysqliFetchCell(&$data){
		$this->stmp->fetch();
		return $data[key($data)];
	}
	private function getParamTypes($arguments){
		unset($arguments[0]);
		$retval='';
		foreach($arguments as $arg)
			$retval.=$this->getTypeByVal($arg);
		return $retval;
	}
	protected function getTypeByVal($variable){
		switch(gettype($variable)){
			case 'integer':
				$type='i';
				break;
			case 'double':
				$type='d';
				break;
			default:
				$type='s';
		}
		return $type;
	}
	public function _getObject(){
		return $this->db_resource;
	}
	public function __destruct(){
		$this->db_resource->close();
	}
}

function db_connect(){
	global $DB;
	global $DB_CONF;
	$DB=new simpleMysqli($DB_CONF);
};

function db_disconnect(){
	global $DB;
	unset($DB);
};


function db_getItems($q,$conn=NULL){
	$res=db_query($q);
	if(!$res)
		return Array();
	return $res;
};

function db_getItem($q,$conn=NULL){
	$res=db_query($q);
	if (count($res)==1)
		return $res[0];
	if(!$res)
		return Array();
	return $res;
};

function db_query($sql){
	$sql=trim($sql);
	global $DB;
	switch(current(explode(" ",$sql,2))){
		case "INSERT":
			$DB->insert($sql);
			$q=db_insert_id();
		break;
		case "SHOW":
		case "SELECT":
			$q=$DB->select($sql);
		break;
		case "UPDATE":
			$DB->update($sql);
			$q=db_affected_rows();
		break;
		case "DELETE":
			$DB->delete($sql);
			$q=db_affected_rows();
		break;
		case "CREATE":
			$DB->other_query($sql);
			$q=true;
		break;
		default:
			$q=0;
		break;
	}
	return $q;
}

function db_selectCol($sql){ //столбец
	global $DB;
	return $DB->selectCol($sql);
};

function db_affected_rows(){ 
	global $DB;
	return $DB->queryInfo['affected_rows'];
};

function db_insert_id(){
	global $DB;
	return $DB->queryInfo['insert_id'];
};

function db_num_rows(){ //число записей
	global $DB;
	return $DB->queryInfo['num_rows'];
};

function db_field_count(){
	global $DB;
	return $DB->queryInfo['field_count'];
};

function db_sql_state(){
	global $DB;
	return $DB->queryInfo['sqlstate'];
};

function db_show_state(){ //статус запроса
	global $DB;
	echo '<pre>';
	print_r($DB->queryInfo);
	echo '</pre>';
};

function db_transaction($sql_array=Array()){ // для INNODB
	global $DB;
	if(count($sql_array)){
		$state=1;
		$DB->transactionStart();
		foreach($sql_array as $sql){
			$state=(!db_query($sql)&&(db_sql_state()!='00000'))?0:$state;
		}
		if($state)
			$DB->transactionCommit();
		else
			$DB->transactionRollBack();
		return $state;
	} else
		return -1;
};
?>