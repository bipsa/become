<?php 
	
	
	/**
	* @autor Sebastian Romero
	* @how to use :
	* $ds = new DataSet();
	* $ds->push("hola");
	* $ds->push("jelou");
	* $ds->remove("hola");
	* $pag = $ds->limit(3, 2);
	**/
	
	class DataSet {
		
		private $arr_dataProvider = Array();
		
		
		/**
		* Constructor 
		**/
		public function __construct(){
			
		}
		
		
		/**
		* push and element into array
		*/
		public function push($data){
			if($data)
				array_push($this->arr_dataProvider, $data);
			return $this;
		}
		
		/**
		 * 
		 */
		public function clear(){
			$this->arr_dataProvider = Array();
		}
		
		/**
		* removes the given elemnet in the object
		**/
		public function remove($element){
			$itemFound = false;
			$newArr = Array();
			for ($i = 0; $i<count($this->arr_dataProvider); $i++){
				if($element === $this->arr_dataProvider[$i]){
					$itemFound = true;
				} else 
					array_push($newArr, $this->arr_dataProvider[$i]);
			}
			$this->arr_dataProvider = $newArr;
			return $itemFound;
		}
		
		
		
		/**
		* slice the array in a given page, very usefull once you wanna a page the dataset. 
		* Try to do it in the database first this is just a method for dataset
		**/
		public function limit($size, $pageNumber = 0){
			$pageArray = Array();
			$offset = ($pageNumber) * $size;
			$pageArray = array_slice($this->arr_dataProvider, $offset, $size);
			return $pageArray;
		}
		
		
		/**
		**/
		public function getElementByIndex($index = 0){
			return $this->arr_dataProvider[$index];
		}
		
		
		/**
		*
		**/
		public function getElementByName($name){
			$value_ = "";
			for ($i = 0; $i<count($this->arr_dataProvider); $i++){
				foreach ($this->arr_dataProvider[$i] as $key=>$value) {
					if($key === $name){
						$value_ = $value;
						break;
					}
				}
			}
			return $value_;
		}
		
		
		/**
		**/
		public function length(){
			return count($this->arr_dataProvider);	
		}
		
		
		/**
		*
		**/
		public function getData(){
			return $this->arr_dataProvider;
		}
		
		
	}
?>