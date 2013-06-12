<?php 

	require_once('DataSet.php'); 
	
	/**
	* @autor Sebastian Romero 
	* $mysql = new MySQL("localhost", "root", "", "mysql");
	* $mysql->fetch("select * from help_keyword;");
	* for ($i = 0; $i<$mysql->lenght(); $i++){
	*	$item = $mysql->getElementByIndex($i)->getElementByIndex(1);
	*	echo $item["name"] . "<br>";
	* }
	*
	**/
	class MySQL extends DataSet {
		
		
		private $db;
		private $query;
		private $instance;
		private $user;
		private $host;
		private $pass;
		
		/**
		* Constucts a new DataAccess object
		* @param $host string hostname for dbserver
		* @param $user string dbserver user
		* @param $pass string dbserver user password
		* @param $db string database name
		*/
		public function MySQL($host, $user, $pass, $db){
			$this->user = $user;
			$this->host = $host;
			$this->pass = $pass;
			$this->db = $db;
		}
		
		
		/**
		* Fetches a query resources and stores it in a local member
		* @param $sql string the database query to run
		* TODO: Fix when no data is retrieved
		* @return void
		*/
		public function fetch($sql, $insert = false){
			$this->clear();
			$this->instance = mysql_pconnect($this->host,$this->user,$this->pass);
			$this->db = mysql_select_db($this->db,$this->instance);
			$this->query=mysql_unbuffered_query($sql, $this->instance);
			$ds = new DataSet();
			$id = 0;
			if(!$insert){
				if ($row=@mysql_fetch_array($this->query, MYSQL_ASSOC))
					$ds = $this->dataSet($row);
				mysql_close($this->instance);
				return $ds;
			} else {
				mysql_query($this->query);
				$id = mysql_insert_id();
				mysql_close($this->instance);
				return $id;
			}
		}
		
		
		/**
		 * 
		 * Load a dataset and return JSON format
		 */
		public function encode($ds){
			$json = "[";
			for($i = 0; $i<$ds->length(); $i++ ){
				$json .= json_encode( $ds->getElementByIndex($i)->getData());
				if($i < ($ds->length() - 1))
					$json .= ",";
			}
			$json .= "]";
			return $json;
		}
		
		
		/**
		*
		**/
		public function insert($sql){
			$lastId = $this->fetch($sql, true);
			return $lastId;
		}
		
		/**
		*
		**/
		public function update($sql){
			$lastId = $this->fetch($sql, true);
			return $lastId;
		}
		
		/**
		*
		**/
		public function delete($sql){
			$lastId = $this->fetch($sql, true);
			return $lastId;
		}
		
		
		/**
		* Returns an Associative Array of Objects
		* @return a DataSet
		*/
		private function dataSet($arr) {
			$data = Array(); $tags = new DataSet(); $itemSet = new DataSet();
			foreach ($arr as $key=>$value) {
				$tags->push($key);
				$item = array($key => $value);
				$itemSet->push($item);
			}
			$this->push($itemSet);
			while ($row = mysql_fetch_array($this->query)){
				$itemSet = new DataSet();
				for ($i=0; $i<$tags->length(); $i++){
					$item = array($tags->getElementByIndex($i) => utf8_encode($row[$tags->getElementByIndex($i)]));
					$itemSet->push($item);
				}
				$this->push($itemSet);
			}
			return $this; 
		}
		
		/**
		* Returns the connection instance
		* @return a MysqlConnect Object
		*/
		public function getInstance(){
			return $this->instance;
		}
		
		/**
		* sets a query in the object
		* @param sql query
		*/
		public function setQuery($query){
			$value;
			if($this->db)
				$value = $this->fetch($query);
			else 
				$value= mysql_query($query, $this->instance);
			return $value;
		}
	}
	
?>