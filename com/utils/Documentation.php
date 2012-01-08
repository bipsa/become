<?php


	/**
	* @autor Sebastian Romero
	* @date March 17th 2010
	*/
	
	class Documentation {
		
		
		private $pathFile;
		private $comments;
		
		
		public function Documentation($path){
			$this->pathFile = $path;
			$this->comments = $this->getComments($this->pathFile);
			for ($count = 0; $count < count($this->comments[0]); $count++){
				$autor = $this->getGuide($this->comments[0][$count], "param");
				print_r($autor);
			}
		}
		
		
		/**
		* @method Gets the comment
		*
		**/
		public function getComments($path) {
			$comments = array();
			$script = file_get_contents($path, "r");
			preg_match_all("/(?s)(\/\*.*?\*\/)/", $script, $comments);
			return $comments;
		}
		
		
		/**
		*
		* @method Gets the function information
		**/
		public function getMethods($path){
			$script = file_get_contents($path, "r");
			$methods = preg_split("/(?s)(\/\*.*?\*\/)/", $script);
			return $methods;
		}
		
		
		/**
		*
		**/
		public function getGuide($comment, $guide){
			$myGuide = array();
			//preg_match_all("/@(?:". $guide .")\s([\w \t]+)\n/", $comment, $myGuide);
			preg_match_all("/@(?:". $guide .")\s(.+)\W/", $comment, $myGuide);
			return $myGuide;
		}
			
		
		
	}
	

?>